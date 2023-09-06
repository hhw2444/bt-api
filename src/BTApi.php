<?php
namespace Root\BtApi;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Root\BtApi\Entitys\SiteEntity;

class BTApi //implements BTApiInterface
{
    protected string $base_uri;

    protected string $key;

    protected string $remark;
    protected int $site_type;

    protected string $ssl_path;

    protected string $domain;

    protected array $proxy_info;

    protected array $new_domain;

    protected bool $is_init = false;

    public function __construct(string $base_uri, string $key)
    {
        $this->base_uri = $base_uri;
        $this->key = $key;
    }

    public function init(...$value): static
    {
        if (!empty($value)) {
            $param = $this->formatParams($value);
            foreach ($param as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
        }
        $this->is_init = true;
        return $this;
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function addApiSite(): bool
    {
        if ($this->is_init === false) {
            throw new Exception(message: '服务未初始化');
        }
        $site_result=  $this->addSite();

        $this->logsOpen($site_result['siteId'])
            ->addDomain($site_result['siteId'])
            ->setSSL()
            ->httpToHttps()
            ->createProxy();
        return true;
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function addWebSite(): bool
    {
        if ($this->is_init === false) {
            throw new Exception(message: '服务未初始化');
        }
        $site_result=  $this->addSite();

        $this->logsOpen($site_result['siteId'])
            ->setSSL()
            ->httpToHttps()
            ->setSiteRunPath(id: $site_result['siteId'], run_path: '/dist_production');
        return true;
    }

    /**
     * @throws GuzzleException
     */
    public function getSiteTypes(): array
    {
        $sign = $this->sign();
        $form_data = array_merge([], $sign);
        $send_data = [];
        foreach ($form_data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/site?action=get_site_types",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        return json_decode($json_response_result, true);
    }

    /**
     * @throws GuzzleException
     */
    public function addSiteType(string $name): bool
    {
        $form_data = [
            'name' => $name,
        ];
        $sign = $this->sign();
        $form_data = array_merge($form_data, $sign);
        $send_data = [];
        foreach ($form_data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/site?action=add_site_type",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        $data_response_result = json_decode($json_response_result, true);
        return $data_response_result['status'] ?? false;
    }

    /**
     * @throws GuzzleException
     */
    public function removeSiteTypes(int $id): bool
    {
        $form_data = [
            'id' => $id,
        ];
        $sign = $this->sign();
        $form_data = array_merge($form_data, $sign);
        $send_data = [];
        foreach ($form_data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/site?action=remove_site_type",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        $data_response_result = json_decode($json_response_result, true);
        return $data_response_result['status'] ?? false;
    }

    public function getSign(): array
    {
        return $this->sign();
    }


    /**
     * 创建站点
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    public function addSite(): array
    {
        if ($this->is_init === false) {
            throw new Exception(message: '服务未初始化');
        }
        $siteEntity = new SiteEntity([
            'webname' => json_encode(['domain' => $this->domain, 'domainlist' => [], 'count' => 0]),
            'path' => "/www/wwwroot/{$this->domain}",
            'ps' => $this->remark,
            'type_id' => $this->site_type,
        ]);
        $site_entity = $siteEntity->toArray();
        $sign = $this->sign();
        $data = array_merge($site_entity, $sign);
        $send_data = [];
        foreach ($data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/site?action=AddSite",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        $data_response_result = json_decode($json_response_result, true);
        $check = $data_response_result['siteStatus'] ?? false;
        if ($check === false) {
            $message = $data_response_result['msg'] ?? '创建站点失败';
            throw new Exception(message: $message, code: 100010);
        }
        return $data_response_result;
    }

    /**
     * 开启/关闭 日志写入
     * @param int $site_id
     * @return $this
     * @throws GuzzleException
     * @throws Exception
     */
    public function logsOpen(int $site_id): static
    {
        if ($this->is_init === false) {
            throw new Exception(message: '服务未初始化');
        }
        $sign = $this->sign();
        $data = array_merge(['id' => $site_id], $sign);
        $send_data = [];
        foreach ($data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/site?action=logsOpen",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        $data_response_result = json_decode($json_response_result, true);
        $check = $data_response_result['status'] ?? false;
        if ($check === false) {
            $message = $data_response_result['msg'] ?? '关闭/开启日志写入失败';
            throw new Exception(message: $message, code: 100020);
        }
        return $this;
    }

    /**
     * 创建反向代理
     * @return $this
     * @throws GuzzleException
     * @throws Exception
     */
    public function createProxy(): static
    {
        if ($this->is_init === false) {
            throw new Exception(message: '服务未初始化');
        }
        if (empty($this->proxy_info)) {
            throw new Exception(message: '反向代理未配置基础信息', code: 100031);
        }
        $form_data = [
            'type' => 1,
            'cachetime' => 1,
            'cache' => 0,
            'advanced' => 0,
            'todomain' => '$host',
            'subfilter' => json_encode([['sub1' => '', 'sub2' => ''],['sub1' => '', 'sub2' => ''],['sub1' => '', 'sub2' => '']]),
            'proxydir' => $this->proxy_info['proxydir'] ?? '/',
            'proxysite' => $this->proxy_info['proxysite'],
            'proxyname' => $this->proxy_info['proxyname'],
            'sitename' => $this->domain,
        ];
        $sign = $this->sign();
        $form_data = array_merge($form_data, $sign);
        $send_data = [];
        foreach ($form_data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/site?action=CreateProxy",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        $data_response_result = json_decode($json_response_result, true);
        $check = $data_response_result['status'] ?? false;
        if ($check === false) {
            $message = $data_response_result['msg'] ?? '添加反向代理失败';
            throw new Exception(message: $message, code: 100030);
        }
        return $this;
    }

    /**
     * 配置SSL证书
     * @return $this
     * @throws GuzzleException
     * @throws Exception
     */
    public function setSSL(): static
    {
        if ($this->is_init === false) {
            throw new Exception(message: '服务未初始化');
        }
        if (!file_exists("{$this->ssl_path}/privkey.key")) {
            return $this;
//            throw new Exception(message: "密钥不存在。请检查：{$this->ssl_path}/privkey.key");
        }
        if (!file_exists("{$this->ssl_path}/fullchain.pem")) {
            return $this;
//            throw new Exception(message: "证书不存在。请检查：{$this->ssl_path}/fullchain.pem");
        }
        $key = file_get_contents("{$this->ssl_path}/privkey.key");
        $csr = file_get_contents("{$this->ssl_path}/fullchain.pem");
        $form_data = [
            'type' => 1,
            'siteName' => $this->domain,
            'key' => $key,
            'csr' => $csr,
        ];
        $sign = $this->sign();
        $form_data = array_merge($form_data, $sign);
        $send_data = [];
        foreach ($form_data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/site?action=SetSSL",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        $data_response_result = json_decode($json_response_result, true);
        $check = $data_response_result['status'] ?? false;
        if ($check === false) {
            $message = $data_response_result['msg'] ?? '设置证书失败';
            throw new Exception(message: $message, code: 100040);
        }
        return $this;
    }

    /**
     * 强制HTTPS
     * @return $this
     * @throws GuzzleException
     * @throws Exception
     */
    public function httpToHttps(): static
    {
        if ($this->is_init === false) {
            throw new Exception(message: '服务未初始化');
        }
        $form_data = [
            'siteName' => $this->domain,
        ];
        $sign = $this->sign();
        $form_data = array_merge($form_data, $sign);
        $send_data = [];
        foreach ($form_data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/site?action=HttpToHttps",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        $data_response_result = json_decode($json_response_result, true);
        $check = $data_response_result['status'] ?? false;
        if ($check === false) {
            $message = $data_response_result['msg'] ?? '开启强制HTTPS失败';
            throw new Exception(message: $message, code: 100050);
        }
        return $this;
    }

    /**
     * 设置站点运行目录
     * @param int $id
     * @param string $run_path
     * @return $this
     * @throws GuzzleException
     * @throws Exception
     */
    public function setSiteRunPath(int $id, string $run_path): static
    {
        if ($this->is_init === false) {
            throw new Exception(message: '服务未初始化');
        }
        $form_data = [
            'id' => $id,
            'runPath' => $run_path,
        ];
        $sign = $this->sign();
        $form_data = array_merge($form_data, $sign);
        $send_data = [];
        foreach ($form_data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/site?action=SetSiteRunPath",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        $data_response_result = json_decode($json_response_result, true);
        $check = $data_response_result['status'] ?? false;
        if ($check === false) {
            $message = $data_response_result['msg'] ?? '设置站点运行目录失败';
            throw new Exception(message: $message, code: 100060);
        }
        return $this;
    }

    /**
     * 添加域名
     * @param int $id
     * @return $this
     * @throws GuzzleException
     * @throws Exception
     */
    public function addDomain(int $id): static
    {
        if ($this->is_init === false) {
            throw new Exception(message: '服务未初始化');
        }
        if (empty($this->new_domain)) {
            return $this;
        }
        $form_data = [
            'id' => $id,
            'webname' => $this->domain,
            'domain' => implode(',', $this->new_domain),
        ];
        $sign = $this->sign();
        $form_data = array_merge($form_data, $sign);
        $send_data = [];
        foreach ($form_data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/site?action=AddDomain",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        $data_response_result = json_decode($json_response_result, true);
        if (!is_array($data_response_result) || !isset($data_response_result['domains'])) {
            $message = $data_response_result['msg'] ?? '添加域名失败';
            throw new Exception(message: $message, code: 100071);
        }
        $check = true;
        $error_msg = '';
        foreach ($data_response_result['domains'] as $value) {
            if ($value['status'] === false) {
                $check = false;
                $error_msg .= "域名：{$value['name']}，失败原因：{$value['msg']}\r\n";
            }
        }
        if ($check === false) {
            throw new Exception(message: $error_msg, code: 100070);
        }
        return $this;
    }



    /**
     * 获取签名
     * @return array
     */
    private function sign(): array
    {
        $now_time = time();
        $token = md5(((string) $now_time) . md5($this->key));
        return [
            'request_time' => $now_time,
            'request_token' => $token,
        ];
    }



    private function formatParams($value): array
    {
        if (isset($value[0])) {
            $value = $value[0];
        }
        if (! is_array($value)) {
            $value = ['value' => $value];
        }
        return $value;
    }

    /**
     * 检测上传文件是否存在
     * @param string $file_name 要检测的文件名
     * @return $this
     * @throws GuzzleException
     */
    public function uploadFileExists(string $file_name): static
    {
        if ($this->is_init === false) {
            throw new Exception(message: '服务未初始化', code: 100081);
        }
        if (empty($this->domain)) {
            throw new Exception(message: '站点未初始化', code: 100082);
        }
        $filename = "/www/wwwroot/{$this->domain}/{$file_name}";
        $form_data = [
            'filename' => $filename,
        ];
        $sign = $this->sign();
        $form_data = array_merge($form_data, $sign);
        $send_data = [];
        foreach ($form_data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/files?action=upload_file_exists",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        $data_response_result = json_decode($json_response_result, true);
        $check = $data_response_result['status'] ?? false;
        if ($check === false) {
            $message = $data_response_result['msg'] ?? '未知错误';
            throw new Exception(message: $message, code: 100083);
        }
        return $this;
    }


    /**
     * 上传文件
     * @param string $f_name 要上传的文件名
     * @param int $original_path 原文件目录（不带文件名）
     * @param int $f_size 要上传的文件大小
     * @param int $f_start 上传起始值（默认0）
     * @return $this
     * @throws GuzzleException
     */
    public function upload(string $f_name, int $original_path, int $f_size, int $f_start = 0): static
    {
        if ($this->is_init === false) {
            throw new Exception(message: '服务未初始化', code: 100091);
        }
        if (empty($this->domain)) {
            throw new Exception(message: '站点未初始化', code: 100092);
        }
        $f_path = "/www/wwwroot/{$this->domain}/";
        $form_data = [
            'f_name' => $f_name,
            'f_size' => $f_size,
            'f_path' => $f_path,
            'f_start' => $f_start
        ];
        $sign = $this->sign();
        $form_data = array_merge($form_data, $sign);
        $send_data = [];
        foreach ($form_data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $send_data[] = [
            'name' => 'blob',
            'contents' => file_get_contents("$original_path/$f_name"),
            'filename' => $f_name
        ];
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/files?action=upload",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        $data_response_result = json_decode($json_response_result, true);
        $check = $data_response_result['status'] ?? false;
        if ($check === false) {
            $message = $data_response_result['msg'] ?? '未知错误';
            throw new Exception(message: $message, code: 100093);
        }
        return $this;
    }


    /**
     * 解压文件
     * @param string $sfile 压缩包文件
     * @param string $dfile 解压到
     * @param string $type 压缩包类型
     * @param string $coding 编码默认（UTF-8、GBK）
     * @param string $password 解压密码
     * @return $this
     * @throws GuzzleException
     */
    public function unZip(string $sfile, string $dfile, string $type, string $coding = 'UTF-8', string $password = ''): static
    {
        if ($this->is_init === false) {
            throw new Exception(message: '服务未初始化', code: 100081);
        }
        if (empty($this->domain)) {
            throw new Exception(message: '站点未初始化', code: 100082);
        }
        $form_data = [
            'sfile' => $sfile,
            'dfile' => $dfile,
            'type' => $type,
            'coding' => $coding,
            'password' => $password,
        ];
        $sign = $this->sign();
        $form_data = array_merge($form_data, $sign);
        $send_data = [];
        foreach ($form_data as $key => $datum) {
            $send_data[] = [
                'name' => $key,
                'contents' => $datum
            ];
        }
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);
        $options = [
            'base_uri' => "$this->base_uri/files?action=UnZip",
            RequestOptions::MULTIPART => $send_data,
        ];
        $response = $client->request('POST', '', $options);
        $json_response_result = $response->getBody()->getContents();
        $data_response_result = json_decode($json_response_result, true);
        $check = $data_response_result['status'] ?? false;
        if ($check === false) {
            $message = $data_response_result['msg'] ?? '未知错误';
            throw new Exception(message: $message, code: 100083);
        }
        return $this;
    }
}