<?php

namespace App\Services;

use Google\Client;
use Google\Service\AnalyticsData;
use Google\Service\AnalyticsData\RunRealtimeReportRequest;
use Google\Service\AnalyticsData\Metric;

class GoogleAnalyticsService
{
    protected $analytics;
    protected $propertyId;

    public function __construct()
    {
        $this->propertyId = env('GA4_PROPERTY_ID');

        $client = new Client();
        $client->setAuthConfig(storage_path('app/analytics/service-account.json'));
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        $this->analytics = new AnalyticsData($client);
    }

    /**
     * Get real-time active users count from GA4
     */
    public function getRealtimeActiveUsers()
    {
        $metric = new Metric();
        $metric->setName('activeUsers');

        $request = new RunRealtimeReportRequest();
        $request->setMetrics([$metric]);

        $response = $this->analytics->properties->runRealtimeReport(
            'properties/' . $this->propertyId,
            $request
        );

        if (!empty($response->getRows())) {
            return (int) $response->getRows()[0]->getMetricValues()[0]->getValue();
        }

        return 0;
    }
}
