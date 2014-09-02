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

namespace OCA\ocUsageCharts\Service\DataProviders;

class StorageUsageCurrentProvider extends StorageUsageBase implements DataProviderInterface
{
    /**
     * Return the chart data you want to return based on the ChartConfig
     *
     * @return mixed
     */
    public function getChartUsage()
    {
        $new = array();
        $storageInfo = \OC_Helper::getStorageInfo('/');
        $free = ceil($storageInfo['free'] / 1024 / 1024);
        if ( $this->isAdminUser() )
        {
            $data = $this->repository->findAll(1);
            foreach($data as $username => $items)
            {
                foreach($items as $item)
                {
                    $new[$username] = ceil($item->getUsage() / 1024 / 1024);
                }
            }
            $new['free'] = $free;
            $data = $new;
        }
        else
        {
            $free = ceil($storageInfo['free'] / 1024 / 1024);
            $used = ceil($storageInfo['used'] / 1024 / 1024);
            $data = array(
                'used' => $used,
                'free' => $free
            );
        }
        return $data;
    }
}