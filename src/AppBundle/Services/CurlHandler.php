<?php

namespace AppBundle\Services;

use Curl\MultiCurl;
use \phpQuery;

class CurlHandler
{
	/**
	 * @var bool|integer
	 */
	public $result = false;

	/**
	 * @var MultiCurl
	 */
	protected $multiCurl;

	/**
	 * @var int
	 */
	protected $deepParse = 100;

	/**
	 * @var string
	 */
	protected $keyword;

	/**
	 * @var string
	 */
	protected $domainName;

	/**
	 * @var string
	 */
	protected $proxy;

	/**
	 * @var string
	 */
	protected $proxyType = '';

	/**
	 * @var array
	 */
	protected $errors = array();

	/**
	 * CurlHandler constructor.
	 *
	 * @param string $keyword
	 * @param string $domainName
	 * @param string $proxy
	 * @param string $proxyType
	 */
	public function __construct( $keyword, $domainName, $proxy, $proxyType )
	{
		$this->multiCurl = new MultiCurl;
		$this->keyword = $keyword;
		$this->domainName = $domainName;
		$this->proxy = $proxy;

		if ( $proxyType ) {
			$this->proxyType = "{$proxyType}://";
		}

		$this->multiCurlWork();
	}

	public function multiCurlWork()
	{
		$this->curlSettings();
		$max = $this->deepParse - 10;

		for ( $i = 0; $i <= $max; $i += 10 ) {
			$this->multiCurl->addGet( 'http://www.google.com/search', [
				'hl' => 'ru',
				'tbo' => 'd',
				'site' => '',
				'source' => 'hp',
				'q' => $this->keyword,
				'start' => $i
			] );
		}

		$this->curlSuccess();

		$this->curlError();

		$this->multiCurl->start();
	}

	public function curlSettings()
	{
		$this->multiCurl->setOpts( [
			CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.205 Safari/534.16',
			CURLOPT_REFERER, 'https://www.google.com',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true
		] );

		if ( $this->proxy ) {
			$this->multiCurl->setOpt( CURLOPT_PROXY, $this->proxyType . $this->proxy );
		}
	}

	public function curlSuccess()
	{
		$this->multiCurl->success( function( $instance ) {

			$document = phpQuery::newDocument( $instance->response );

			$cites = $document->find( 'cite' );
			$nav = $document->find( '.cur' );

			$place = '';
			$page = '';

			foreach ( $cites as $key => $cite ) {
				$pq = pq( $cite );

				if ( stripos( $pq->text(), $this->domainName ) !== false ) {
					$place = $key + 1;

					foreach ( $nav as $td ) {
						$pq = pq( $td );

						$page = $pq->text();
					}
					break;
				}
			}

			if ( $place && $page ) {
				$this->result = $page * 10 - 10 + $place;
			}

		} );
	}

	public function curlError()
	{
		$this->multiCurl->error( function( $instance ) {
			echo $instance->errorMessage;
			$this->errors[] = array(
				'url' => $instance->url,
				'code' => $instance->errorCode,
				'message' => $instance->errorMessage
			);
		});
	}
}