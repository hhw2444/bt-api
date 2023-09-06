<?php
namespace Root\BtApi;
interface BTApiInterface
{
    /**
     * 初始化
     * @param ...$value
     * @return $this
     */
    public function init(...$value): static;
    /**
     * 一键生成API站点
     * @return bool
     */
    public function addApiSite(): bool;

    /**
     * 一键生成web站点
     * @return bool
     */
    public function addWebSite(): bool;

    /**
     * 获取站点分类列表
     * @return array
     */
    public function getSiteTypes(): array;

    /**
     * 添加站点分类
     * @param string $name
     * @return bool
     */
    public function addSiteType(string $name): bool;

    /**
     * 删除站点分类
     * @param int $id
     * @return bool
     */
    public function removeSiteTypes(int $id): bool;

    /**
     * 上传并解压文件到指定目录
     * @param string $file_name             文件名
     * @param string $original_path         原文件目录（不带文件名）
     * @param int $size                     文件大小
     * @param string $file_type             文件类型
     * @param string $dfile                 要解压到哪个目录下（只需要填最后的目录例如：想保存到/www/wwwroot/oyvro9.zyx268.com/xxxx  则只需要填 xxxx）
     * @return bool
     */
    public function uploadAndUnzip(string $file_name, string $original_path, int $size, string $file_type, string $dfile): bool;
}