<?php
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');


/**
 * Class McsData
 *
 * like a STRUCT
 */
class McsData
{

    /**
     * Component's settings
     * @var JConfig
     */
    private static $compSettings = null;

    /**
     * Module's settings
     * @var JConfig
     */
    private static $modSettings = null;

    /**
     * ID of current city
     * @var string
     */
    private static $cityId = 0;

    /**
     * Code of current city (subdomain)
     * @var string
     */
    private static $city = '';

    /**
     * Name of current city
     * @var string
     */
    private static $cityName = '';

    /**
     * Will set to TRUE if a city was already selected by user
     * @var bool
     */
    private static $isUserHasSelected = false;

    /**
     * If need redirect to some subdomain
     * @var null
     */
    private static $needRedirectTo = null;

    /**
     * Will set to FALSE for subdomains
     * @var bool
     */
    private static $isBaseDomain = true;

    /**
     * @var string
     */
    private static $cookieDomain = '';

    /**
     * @var int
     */
    private static $moduleId = 0;

    /**
     * @var string
     */
    private static $http = 'http://';


    /**
     * Returns any MCS data/parameter by name
     * @param string $name It may be a http, moduleId, cookieDomain, cityName, city, cities, provinces, countries, basedomain, default_city, needRedirectTo
     *          or any of parameter names of component config of module.
     * @param $default
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        if (property_exists('McsData', $name)) {
            return self::${$name};
        } else if (!empty(self::$compSettings) && self::$compSettings->get($name) !== null) {
            return self::$compSettings->get($name);
        } else if (!empty(self::$modSettings) && self::$modSettings->get($name) !== null) {
            return self::$modSettings->get($name);
        }
        return $default;
    }


    /**
     * For tests
     */
    public static function set($name, $value)
    {
        if (property_exists('McsData', $name)) {
            self::${$name} = $value;
        } else if (!empty(self::$compSettings) && self::$compSettings->get($name) !== null) {
            self::$compSettings->set($name, $value);
        } else if (!empty(self::$modSettings) && self::$modSettings->get($name) !== null) {
            self::$modSettings->set($name, $value);
        }
    }


    /**
     * Loads all options of extension
     */
    public static function load()
    {
        // load component settings
        self::$compSettings = JComponentHelper::getParams('com_mycityselector');
        // load module settings
        $query = JFactory::getDbo()->getQuery(true);
        $query->select('params')->from('#__modules')->where("module = 'mod_mycityselector' LIMIT 1");
        $query = JFactory::getDbo()->setQuery($query);
        $result = $query->loadResult();
        self::$modSettings = new \JRegistry($result); // alias of Joomla\Registry\Registry in libraries/vendor/joomla/registry/src/Registry.php
        // определяем ID модуля
        $query = JFactory::getDbo()->getQuery(true);
        $query->select('id')->from('#__modules')->where("module = 'mod_mycityselector' LIMIT 1");
        $result = JFactory::getDbo()->setQuery($query)->loadResult();
        self::$moduleId = $result;

        // cookie domain
        if (self::$compSettings->get('subdomain_cities') == '1') { // города на поддоменах?
            self::$cookieDomain = '.' . self::$compSettings->get('basedomain');
        } else {
            self::$cookieDomain = self::$compSettings->get('basedomain');
        }
        // http || https ?
        self::$http = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ?
            'https://' : 'http://';
        self::$isUserHasSelected = empty($_COOKIE['MCS_CITY_CODE']) ? false : true; // if cookies exists then used has selected a city
        // current city
        self::detectCurrentCity();
    }


    /**
     * Defines a current city
     */
    private static function detectCurrentCity()
    {
        if (self::get('subdomain_cities') == '1') { // города на поддоменах?
            // проверяем базовый домен
            if (!empty(self::$compSettings) && self::$compSettings->get('basedomain')) {
                $baseDomain = self::$compSettings->get('basedomain');
                McsLog::add('Базовый домен: ' . $baseDomain );
            } else {
                // иначе, пытаемся определить базовый домен самостоятельно
                McsLog::add('Базовый домен не определен', McsLog::WARN);
                $baseDomain = $_SERVER['HTTP_HOST'];
                $parts = explode('.', $baseDomain);
                if (count($parts) > 2) {
                    if ($parts[0] == 'www') {
                        $baseDomain = str_replace('www.', '', $_SERVER['HTTP_HOST']);
                    } else {
                        // возможно, это поддомен. попытаемся его найти в базе
                        $city = self::findCity($parts[0]);
                        if (!empty($city)) {
                            unset($parts[0]); // да это поддомен, удаляем его и остается только имя домена
                        }
                        $baseDomain = implode('.', $parts);
                    }
                }
                McsLog::add('Автоопределение: ' . $baseDomain, McsLog::WARN);
            }
            McsLog::add('Определяем текущий город');
            // проверяем текущий поддомен
            $subDomain = str_replace([$baseDomain, '.'], ['', ''], $_SERVER['HTTP_HOST']);
            if (!empty($subDomain) && $subDomain != 'www') {
                McsLog::add('Ищем город по поддомену');
                $city = self::findCity($subDomain);
                if (!empty($city)) {
                    McsLog::add('Город найден: ' . $city['name']);
                    self::$isBaseDomain = false;
                    self::$cityId = $city['id'];
                    self::$city = $subDomain;
                    self::$cityName = $city['name'];
                } else {
                    McsLog::add('Город ' . $subDomain . ' не найден, отправляем на базовый домен', McsLog::WARN);
                    $default = self::get('default_city');
                    $city = self::findCity($default);
                    if (!empty($city)) {
                        self::$isBaseDomain = false;
                        self::$cityId = $city['id'];
                        self::$city = $subDomain;
                        self::$cityName = $city['name'];
                        self::$needRedirectTo = self::get('http') . self::get('basedomain') . '/';
                    }
                }
            } else {
                // иначе - это базовый домен
                // если включен авторедирект на выбранный ранее город, то
                if (self::get('autoswitch_city') == '1' && !empty($_COOKIE['MCS_CITY_CODE'])) { // проверяем код города в кукисах
                    $city = self::findCity($_COOKIE['MCS_CITY_CODE']);
                    if (!empty($city)) {
                        // если город найден и он не является городом по умолчанию, то даем команду на редирект
                        if (!empty(self::$compSettings) && self::get('default_city')) {
                            $default = self::get('default_city');
                            if ($default != $_COOKIE['MCS_CITY_CODE']) {
                                self::$cityId = $city['id'];
                                self::$city = $_COOKIE['MCS_CITY_CODE'];
                                self::$cityName = $city['name'];
                                self::$needRedirectTo = self::get('http') . self::$city . '.' . self::get('basedomain') . '/';
                            }
                        }
                    } // иначе, см ниже :pointDefault:
                }
            }
        } else {
            McsLog::add('Определяем текущий город');
            if (!empty($_COOKIE['MCS_CITY_CODE'])) {
                McsLog::add('Ищем город по поддомену');
                $city = self::findCity($_COOKIE['MCS_CITY_CODE']);
                if (!empty($city)) {
                    McsLog::add('Город найден: ' . $city['name']);
                    self::$cityId = $city['id'];
                    self::$city = $_COOKIE['MCS_CITY_CODE'];
                    self::$cityName = $city['name'];
                } // иначе, см ниже :pointDefault:
            }
        }

        // :pointDefault:
        // берем город по умолчанию (default_city из настроек компонента)
        if (empty(self::$city)) {
            if (!empty(self::$compSettings) && self::get('default_city')) {
                $city = self::findCity(self::get('default_city'));
                if (!empty($city)) {
                    McsLog::add('Город найден: ' . $city['name']);
                    self::$cityId = $city['id'];
                    self::$city = $city['subdomain'];
                    self::$cityName = $city['name'];
                } else {
                    // да что за нафиг?
                    McsLog::add('Базовый город не определен!', McsLog::WARN);
                    return;
                }
            }
        }

        // обновляем кукис
        setcookie('MCS_CITY_CODE', self::$city, time() + 3600 * 24 * 30, '/', self::$cookieDomain, false, false);
    }


    /**
     * Returns cities from DB by condition
     * @param array $excludes
     * @return array
     */
    public static function getCities($excludes = [], $column = null)
    {
        $db = JFactory::getDbo();
        if (!empty($excludes)) {
            $where = ' `name` NOT IN (';
            foreach ($excludes as $name) {
                $where .= $db->quote($name);
            }
            $where .= ')';
        }
        if (empty($where)) {
            $where = '1 = 1';
        }
        $query = $db->getQuery(true);
        $query->select('*')->from('#__mycityselector_city')->where($where);
        $db->setQuery($query);
        $rows = $db->loadAssocList();
        if (!empty($column)) {
            $rows = array_column($rows, $column);
        }
        return $rows;
    }


    /**
     * @param $code
     * @return mixed|null
     */
    private static function findCity($code)
    {
        $city = null;
        if (!empty($code)) {
            $db = JFactory::getDbo();
            $code = $db->quote($code);
            $query = $db->getQuery(true);
            $query->select('*')->from('#__mycityselector_city')->where("`subdomain` LIKE {$code}");
            $db->setQuery($query);
            $city = $db->loadAssocList();
            if (!empty($city)) {
                $city = $city[0];
            }
        }
        return $city;
    }


    /**
     * Search Item (city or province of cities)
     * @param $name
     * @return mixed|null
     */
    public static function getTypeByName($name)
    {
        $type = null;
        if (!empty($name)) {
            $db = JFactory::getDbo();
            $name = $db->quote('%' . $name . '%');
            $query = $db->getQuery(true);
            $query->select('id')->from('#__mycityselector_city')->where("`name` LIKE {$name}");
            $db->setQuery($query);
            $res = $db->loadAssocList();
            if (!empty($res)) {
                $type = 'city';
            } else {
                $query = $db->getQuery(true);
                $query->select('id')->from('#__mycityselector_province')->where("`name` LIKE {$name}");
                $db->setQuery($query);
                $res = $db->loadAssocList();
                if (!empty($res)) {
                    $type = 'province';
                } else {
                    $query = $db->getQuery(true);
                    $query->select('id')->from('#__mycityselector_country')->where("`name` LIKE {$name}");
                    $db->setQuery($query);
                    $res = $db->loadAssocList();
                    if (!empty($res)) {
                        $type = 'country';
                    }
                }
            }
        }
        return $type;
    }


    /**
     * Loads content
     * @param int $id
     * @param int $cityId optional
     * @return mixed Returns FALSE if content doesn't exists or it's unpublished,
     *                  otherwise it returns a text (content)
     */
    public static function loadContent($id, $cityId = null)
    {
        $db = JFactory::getDbo();
        $id = $db->quote($id);
        if ($cityId === null) {
            $cityId = self::get('cityId');
        }
        $cityId = $db->quote($cityId);
        // сначала ищем текст который соответствует текущему городу
        $query = "SELECT `fv`.`value` FROM `#__mycityselector_field` `fld` "
            . "INNER JOIN `#__mycityselector_field_value` `fv` ON `fv`.`field_id` = `fld`.`id` "
            . "INNER JOIN `#__mycityselector_value_city` `vc` ON `vc`.`fieldvalue_id` = `fv`.`id` "
            . "WHERE `fld`.`id` = {$id} AND `vc`.`city_id` = {$cityId} AND `fv`.`is_ignore` = 0";
        $db->setQuery($query);
        $result = $db->loadAssocList();
        if (!empty($result[0]['value'])) {
            $test = trim(strip_tags($result[0]['value']));
            return empty($test) ? '' : $result[0]['value'];
        } else {
            // теперь смотрим, нет ли отрицаний
            $query = "SELECT `fv`.`value` FROM `#__mycityselector_field` `fld` "
                . "INNER JOIN `#__mycityselector_field_value` `fv` ON `fv`.`field_id` = `fld`.`id` "
                . "INNER JOIN `#__mycityselector_value_city` `vc` ON `vc`.`fieldvalue_id` = `fv`.`id` "
                . "WHERE `fld`.`id` = {$id} AND `fv`.`is_ignore` = 1 "
                . "AND {$cityId} NOT IN (
                    SELECT `vc2`.`city_id` FROM `#__mycityselector_field` `fld2`
                    INNER JOIN `#__mycityselector_field_value` `fv2` ON `fv2`.`field_id` = `fld2`.`id`
                    INNER JOIN `#__mycityselector_value_city` `vc2` ON `vc2`.`fieldvalue_id` = `fv2`.`id`
                    WHERE `fld2`.`id` = {$id} AND `fv2`.`is_ignore` = 1
                )";
            $db->setQuery($query);
            $result = $db->loadAssocList();
            if (!empty($result[0]['value'])) {
                $test = trim(strip_tags($result[0]['value']));
                return empty($test) ? '' : $result[0]['value'];
            }
            // иначе, ищем текст по умолчанию
            $query = "SELECT `fv`.`value` FROM `#__mycityselector_field` `fld` "
                . "INNER JOIN `#__mycityselector_field_value` `fv` ON `fv`.`field_id` = `fld`.`id` "
                . "WHERE `fv`.`field_id` = {$id} AND `fv`.`default` = 1";
            $db->setQuery($query);
            $result = $db->loadAssocList();
            if (!empty($result[0]['value'])) {
                $test = trim(strip_tags($result[0]['value']));
                return empty($test) ? '' : $result[0]['value'];
            }
        }
        return false;
    }

}