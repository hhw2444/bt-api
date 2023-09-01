<?php
namespace Root\BtApi;
use GuzzleHttp\Exception\GuzzleException;

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
class Test{

    /**
     * @throws GuzzleException
     */
    public function testBatchCreate()
    {
        $site_list = $this->getSiteList();
        $service = (new BTApi(
            base_uri: 'https://192.168.56.57:41069',
            key: 'nCUvkCbNgnUVkI0vDR0a4ldhEefBlaz4'
        ));
        foreach ($site_list as $item) {
            $service->init([
                'domain' => $item['domain'],
                'site_type' => $item['site_type'],
                'remark' => $item['remark'],
                'ssl_path' => $item['ssl_path'],
                'proxy_info' => $item['proxy_info'],
                'new_domain' => $item['new_domain'],
            ]);
            if ($item['type'] === 'api') {
                $service->addApiSite();
            } else {
                $service->addWebSite();
            }
        }
        return true;
    }

    /**
     * 获取站点分类列表
     * @throws GuzzleException
     */
    public function testGetSiteType(): array
    {
        $service = (new BTApi(
            base_uri: 'https://192.168.56.57:41069',
            key: 'nCUvkCbNgnUVkI0vDR0a4ldhEefBlaz4'
        ));
        return $service->getSiteTypes();
    }

    /**
     * 添加站点分类
     * @throws GuzzleException
     */
    public function testAddSiteType(): bool
    {
        $service = (new BTApi(
            base_uri: 'https://192.168.56.57:41069',
            key: 'nCUvkCbNgnUVkI0vDR0a4ldhEefBlaz4'
        ));
        return $service->addSiteType(name: '测试');
    }

    /**
     * 删除站点分类
     * @throws GuzzleException
     */
    public function testRemoveSiteType()
    {
        $service = (new BTApi(
            base_uri: 'https://192.168.56.57:41069',
            key: 'nCUvkCbNgnUVkI0vDR0a4ldhEefBlaz4'
        ));
        return $service->removeSiteTypes(id: 1);
    }


    /**
     * @return array[
     *      'domain' => '域名',
     *      'remark' => '站点备注',
     *      'type' => '需要添加的类型(api、web)',
     *      'site_type' => '分类ID',
     *      'ssl_path' => '证书地址（注：必须要配对路径才会生效）',
     *      'proxy_info' => '反向代理',
     *      'new_domain' => '多个域名（不包含domain）',
     * ]
     */
    public function getSiteList()
    {
        $site_list = [
            [
                'domain' => 'z8v9.zyx268.com',
                'remark' => '亿圆乾-短剧-APP通讯,试玩卖量,试玩买量,广告回调,打包列表',
                'type' => 'api',
                'site_type' => 3,
                'ssl_path' => BASE_PATH . '/ssl/zyx_268_com',
                'proxy_info' => [
                    'proxyname' => '反向代理',
                    'proxysite' => 'http://172.16.0.4:9781',
                    'proxydir' => '/'
                ],
                'new_domain' => ['qlomlf.zyx268.com', '86b.zyx268.com', '6p4.zyx268.com']
            ],
            [
                'domain' => 'ftm2pa.zyx268.com',
                'remark' => '亿圆乾-短剧-用户协议,隐私协议,代理端APK',
                'type' => 'api',
                'site_type' => 3,
                'ssl_path' => BASE_PATH . '/ssl/zyx_268_com',
                'proxy_info' => [
                    'proxyname' => '反向代理',
                    'proxysite' => 'http://172.16.0.4:9089',
                    'proxydir' => '/'
                ],
                'new_domain' => ['wb7.zyx268.com', 'fea.zyx268.com']
            ],
            [
                'domain' => 'mbhdn.zyx268.com',
                'remark' => '亿圆乾-短剧-管理后台入口',
                'type' => 'api',
                'site_type' => 2,
                'ssl_path' => BASE_PATH . '/ssl/zyx_268_com',
                'proxy_info' => [
                    'proxyname' => '反向代理',
                    'proxysite' => 'http://172.16.0.4:9089',
                    'proxydir' => '/'
                ],
                'new_domain' => []
            ],
            [
                'domain' => 'cm0.zyx268.com',
                'remark' => '亿圆乾-短剧-代理管理后台入口-非CDN',
                'type' => 'api',
                'site_type' => 2,
                'ssl_path' => BASE_PATH . '/ssl/zyx_268_com',
                'proxy_info' => [
                    'proxyname' => '反向代理',
                    'proxysite' => 'http://172.16.0.4:9089',
                    'proxydir' => '/'
                ],
                'new_domain' => []
            ],
            [
                'domain' => '6pzxn2.zyx268.com',
                'remark' => '亿圆乾-短剧-代理管理后台入口-CDN',
                'type' => 'api',
                'site_type' => 2,
                'ssl_path' => BASE_PATH . '/ssl/zyx_268_com',
                'proxy_info' => [
                    'proxyname' => '反向代理',
                    'proxysite' => 'http://172.16.0.4:9089',
                    'proxydir' => '/'
                ],
                'new_domain' => []
            ],
            [
                'domain' => '2u2.zyx268.com',
                'remark' => '亿圆乾-短剧-管理后台-自动化部署',
                'type' => 'web',
                'site_type' => 1,
                'ssl_path' => BASE_PATH . '/ssl/zyx_268_com',
                'proxy_info' => [],
                'new_domain' => []
            ],
            [
                'domain' => 'jvef.zyx268.com',
                'remark' => '亿圆乾-短剧-代理管理后台-CDN-自动化部署',
                'type' => 'web',
                'site_type' => 1,
                'ssl_path' => BASE_PATH . '/ssl/zyx_268_com',
                'proxy_info' => [],
                'new_domain' => []
            ],
            [
                'domain' => 'oyvro9.zyx268.com',
                'remark' => '亿圆乾-短剧-代理管理后台-非CDN-自动化部署',
                'type' => 'web',
                'site_type' => 1,
                'ssl_path' => BASE_PATH . '/ssl/zyx_268_com',
                'proxy_info' => [],
                'new_domain' => []
            ],
        ];
        return $site_list;
    }
}