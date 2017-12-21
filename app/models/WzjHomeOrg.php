<?php

class WzjHomeOrg extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $org_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $appid;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=false)
     */
    public $shop_id;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $org_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $store_img;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $mendian_img;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $provice;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $city;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $district;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     */
    public $tel;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $address;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $description;

    /**
     *
     * @var string
     * @Column(type="string", length=15, nullable=false)
     */
    public $longitude;

    /**
     *
     * @var string
     * @Column(type="string", length=15, nullable=false)
     */
    public $latitude;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $brand;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $service_type;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $expiration;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $isshare;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $status;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $source;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $add_time;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $sms_num;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $remark;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $principal_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $wechat_name;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=true)
     */
    public $wechat_username;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $wechat_password;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $org_type;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    public $org_lebal;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $ti_status;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $compile_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $marketing_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $auth;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $payment;

    /**
     *
     * @var double
     * @Column(type="double", length=10, nullable=true)
     */
    public $first_money;

    /**
     *
     * @var double
     * @Column(type="double", length=10, nullable=true)
     */
    public $next_money;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $order_time;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $verification;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $is_zb;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $front_status;

    /**
     *
     * @var string
     * @Column(type="string", length=16, nullable=false)
     */
    public $koubei_pid;

    /**
     *
     * @var string
     * @Column(type="string", length=32, nullable=false)
     */
    public $sub_mchid;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $copyright;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $p_org_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $distrib_time;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $business_type;

    /**
     *
     * @var integer
     * @Column(type="integer", length=6, nullable=false)
     */
    public $contract_tpl;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("db_home_weizaojiao");
        $this->setSource("wzj_home_org");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'wzj_home_org';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return WzjHomeOrg[]|WzjHomeOrg|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return WzjHomeOrg|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
