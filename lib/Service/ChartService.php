<?php
/**
 * Copyright (c) 2014 - Arno van Rossum <arno@van-rossum.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace OCA\ocUsageCharts\Service;

use OCA\ocUsageCharts\ChartType\c3jsProvider;
use OCA\ocUsageCharts\ChartType\c3jsProvider\c3js as c3js;
use OCA\ocUsageCharts\ChartType\ChartTypeInterface;
use OCA\ocUsageCharts\Exception\ChartServiceException;

/**
 * @author Arno van Rossum <arno@van-rossum.com>
 */
class ChartService
{
    /**
     * @var ChartDataProvider
     */
    private $provider;

    /**
     * @var ChartConfigService
     */
    private $config;

    /**
     * @var string
     */
    private $currentUserName;

    /**
     * What providers are already loaded
     * @var array
     */
    private $isLoaded = array();

    /**
     * @param ChartDataProvider $provider
     * @param ChartConfigService $config
     * @param string $currentUserName
     *
     * @throws ChartServiceException
     */
    public function __construct(ChartDataProvider $provider, ChartConfigService $config, $currentUserName)
    {
        $this->provider = $provider;
        $this->config = $config;
        if ( empty($currentUserName) )
        {
            throw new ChartServiceException("Invalid user given");
        }
        $this->currentUserName = $currentUserName;
    }

    /**
     * @param string $id
     * @return ChartTypeInterface
     */
    public function getChart($id)
    {
        //return new c3js($id, $this->provider);
        $config = $this->config->getChartConfigById($id);

        // Apparently namespace is needed
        $className = '\OCA\ocUsageCharts\ChartType\c3jsProvider\\' . $config->chartProvider;

        $chart = new $className($config);

        if ( !in_array($config->chartProvider, $this->isLoaded) )
        {
            $chart->loadFrontend();
            $this->isLoaded[] = $config->chartProvider;
        }

        return $chart;
    }

    /**
     * @todo This should be loaded from the config
     *
     *
     * return list of default charts
     *
     * @return array
     */
    public function getCharts()
    {
        //$chartConfig = $this->config->getChartConfig($this->currentUserName);
        return array($this->getChart(1), $this->getChart(2));
    }


    /**
     * @param ChartTypeInterface $chart
     *
     * @return array
     */
    public function getUsage(ChartTypeInterface $chart)
    {
        return $this->provider->getUsage($chart->getConfig());
    }
}
