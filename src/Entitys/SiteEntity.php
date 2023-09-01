<?php

namespace Root\BtApi\Entitys;

class SiteEntity extends BaseEntity
{
    /** @var string 站点名称 */
    protected string $webname;

    /** @var string 站点运行目录 */
    protected string $path;

    /** @var string 备注 */
    protected string $ps;

    /** @var int 分类ID */
    protected int $type_id = 0;

    protected string $type = 'PHP';

    protected string $version = '00';

    protected int $port = 80;

    protected bool $ftp = false;

    protected bool $sql = false;

    protected string $codeing = 'utf8';
}