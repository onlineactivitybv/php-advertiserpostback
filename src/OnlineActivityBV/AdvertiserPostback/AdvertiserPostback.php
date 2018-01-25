<?php
declare(strict_types = 1);
namespace OnlineActivityBV\AdvertiserPostback;

class AdvertiserPostback
{
	protected $clickIdVariableName = null;
	public $cookieName = 'oaClickId';
	public $sessionName = 'oaClickId';
	protected $cookieDomain = null;

	private $adv_hash;
	private $adv_id;

	public function __construct(int $adv_id, string $adv_hash, $variable = 'oa_clickid', $cookieDomain = null)
	{
		$this->clickIdVariableName = $variable;
		$this->cookieDomain = $cookieDomain;

		$this->adv_id = $adv_id;
		$this->adv_hash = $adv_hash;
	}


	protected function startSession($force = false)
	{
		$cookie_exists = isset($_COOKIE['PHPSESSID']);
		$session_started = session_id() != '';

		if(($cookie_exists || $force) && !$session_started) {
			session_set_cookie_params(session_get_cookie_params()['lifetime'], '/', $this->getCookieDomain());
			session_start();
		}
	}

	protected function getCookieDomain() : string
	{
		if($this->cookieDomain) {
			return $this->cookieDomain;
		}

		// get cookie domain from http-host
		if (isset($_SERVER['HTTP_HOST'])) {
			if (preg_match('/(?:www\.|local\.)?([a-zA-Z0-9\.]*)(:\d+)?$/i', $_SERVER['HTTP_HOST'], $matches)) {
				return '.' . $matches[1];
			}
		}

		throw new AdvertiserPostbackException('Cannot detect cookie-domain, please initialize manually');
	}

	public function clickIdFromUrl()
	{
		if(isset($_REQUEST[$this->clickIdVariableName]) && $_REQUEST[$this->clickIdVariableName]) {
			return $_REQUEST[$this->clickIdVariableName];
		}
		return false;
	}

	public function landingPage(bool $setCookies = true, bool $setSession = true) : bool
	{
		if ($clickId = $this->clickIdFromUrl()) {
			if ($setSession) {
				$this->startSession(true);
				$_SESSION[$this->sessionName] = $clickId;
			}

			if ($setCookies) {
				// send p3p header
				header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
				// setcookie
				setcookie($this->cookieName, $clickId, time() + 24*3600*365 , '/', $this->getCookieDomain());
			}
			return true;
		}
		return false;
	}

	public function getClickId($checkSession = true)
	{
		if ($checkSession) {
			// check if session is started
			$this->startSession(false);

			if (isset($_SESSION[$this->sessionName])) {
				return $_SESSION[$this->sessionName];
			}
		}

		if (isset($_COOKIE[$this->cookieName])) {
			return $_COOKIE[$this->cookieName];
		}
		return false;
	}

	public function addConversion($conversionIdentfier,  string $clickId = null, $orderValue = null) : bool
	{
		if (!$clickId) {
			$clickId = $this->getClickId();
		}

		if($clickId) {
			// post conversion to Online Activity
			$q = [
				'adv' => $this->adv_id,
				'idh' => $this->adv_hash,
				'transactie_id' => $conversionIdentfier,
				'clickid' => $clickId,
			];

			$ch = curl_init('http://oa1.nl/callback/click.php?' . http_build_query($q));
			curl_setopt($ch, CURLOPT_USERAGENT, 'OnlineActivity/AdvertiserPostback');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

			$data = curl_exec($ch);
			$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); ;
			curl_close($ch);

			return $responseCode == 200;
		}
		return false;
	}
}