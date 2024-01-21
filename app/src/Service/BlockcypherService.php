<?php

namespace App\Service;

use App\Entity\AddressQuery;
use App\Interface\BlockcypherInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class BlockcypherService implements BlockcypherInterface
{
    private HttpClientInterface $httpClient;
    private const int LIMIT = 50;
    private ParameterBagInterface $parameterBag;
    private EntityManagerInterface $entityManager;

    public function __construct(
        HttpClientInterface $httpClient,
        ParameterBagInterface $parameterBag,
        EntityManagerInterface $entityManager
    ) {
        $this->httpClient = $httpClient;
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string   $asset
     * @param string   $address
     * @param DateTime $endTime
     * @param DateTime $startTime
     *
     * @return Generator
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function query(string $asset, string $address, DateTime $startTime, DateTime $endTime): Generator
    {
        $hasMore = true;

        $before = $endTime->format('Y-m-d');
        $after = $startTime->format('Y-m-d');

        while ($hasMore) {
            $apiUrl = sprintf("https://api.blockcypher.com/v1/%s/main/addrs/%s/full?before=%s&after=%s&limit=%d", $asset, $address, $before, $after, self::LIMIT);
            // Make the API request
            $response = $this->httpClient->request('GET', $apiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Token' => $this->parameterBag->get('api_token'),
                ],
            ]);

            $data =  $response->toArray();
            $hasMore = array_key_exists('hasMore', $data) && $data['hasMore'];
            if ($hasMore) {
                $lastTransaction = $data['txs'][array_key_last($data['txs'])];
                $before = $lastTransaction['block_height'];
            }

            yield $data['txs'];
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ExceptionInterface
     */
    public function getData(string $asset, string $address, DateTime $startTime, DateTime $endTime, int $threshold): array
    {
        $data = $this->fetchDataFromDB($asset, $address, $startTime, $endTime, $threshold);
        if (count($data)) {
            return $data;
        }

        $filteredTransactions = [];
        foreach (
            $this->query($asset, $address, $startTime, $endTime) as
            $transactions
        ) {
            // Filter transactions based on the specified threshold
            $filteredTransactions = array_merge(
                $filteredTransactions,
                array_filter(
                    $transactions,
                    function ($transaction) use ($threshold) {
                        return $transaction['total'] > $threshold;
                    }
                )
            );
        }

        // Calculate transaction count and average transaction quantity
        $transactionCount = count($filteredTransactions);
        $totalTransactionQuantity = array_sum(
            array_column($filteredTransactions, 'total')
        );
        $averageTransactionQuantity = $transactionCount > 0 ? $totalTransactionQuantity / $transactionCount : 0;


        //save data into DB
        $addressQuery = new AddressQuery();
        $addressQuery
            ->setAsset($asset)
            ->setAddress($address)
            ->setBefore($endTime)
            ->setAfter($startTime)
            ->setThreshold($threshold)
            ->setTransactionCount($transactionCount)
            ->setAverageTransactionQuantity($averageTransactionQuantity)
        ;

        $this->entityManager->persist($addressQuery);
        $this->entityManager->flush();


        return [
            'transactionCount' => $transactionCount,
            'averageTransactionQuantity' => $averageTransactionQuantity,
        ];
    }

    /**
     * @throws ExceptionInterface
     */
    private function fetchDataFromDB (string $asset, string $address, DateTime $startTime, DateTime $endTime, int $threshold): array
    {
        // search Data into DB
        $data = $this->entityManager->getRepository(AddressQuery::class)
            ->getAddressQuery(
                $asset,
                $address,
                $endTime,
                $startTime,
                $threshold
            );

        if ($data) {
            $serializer = new Serializer([new ObjectNormalizer()]);
            return $serializer->normalize($data);
        }

        return [];
    }
}