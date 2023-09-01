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

}