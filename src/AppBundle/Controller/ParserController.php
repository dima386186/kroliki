<?php

namespace AppBundle\Controller;

use AppBundle\Services\CurlHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Forms\ParserForm;

class ParserController extends Controller
{
	/**
	 * @Route( "/", name="parserMainPage" )
	 *
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function mainAction( Request $request )
	{
		$form = $this->createForm( ParserForm::class );
		$form->handleRequest( $request );

		$proxy     = $request->get( 'proxy' );
		$proxyType = $request->get( 'proxyType' );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$data       = $form->getData();
			$keyword    = $data->getKeyword();
			$domainName = $data->getDomainName();

			$curlHandler = new CurlHandler( $keyword, $domainName, $proxy, $proxyType );
			$result      = $curlHandler->result ? $curlHandler->result : 0;

			$this->mainSystem( $curlHandler->errors, $result, $data );
		}

		return $this->render( 'parser/main.html.twig', [
			'form'       => $form->createView(),
			'proxy'      => $proxy,
			'proxyType'  => $proxyType
		] );
	}

	/**
	 * @Route( "/parserHistory", name="parserHistory" )
	 *
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function historyAction( Request $request )
	{
		$histories = $this->getDoctrine()->getRepository( 'AppBundle:ParserHistory' )->findBy( [], [ 'sampleDate' => 'DESC' ] );

		$paginator  = $this->get( 'knp_paginator' );
		$pagination = $paginator->paginate(
			$histories,
			$request->query->getInt( 'page', 1 ),
			$request->query->getInt( 'limit', 10 )
		);

		return $this->render( 'parser/history.html.twig', [
			'histories' => $pagination
		] );
	}

	/**
	 * @param object $history
	 * @param integer $result
	 */
	protected function insertHistory( $history, $result )
	{
		$em = $this->getDoctrine()->getManager();

		$history->setPosition( $result );
		$em->persist( $history );

		$em->flush();
	}

	/**
	 * @param array $errors
	 * @param integer $result
	 * @param object $data
	 */
	protected function mainSystem( $errors, $result, $data )
	{
		if ( count( $errors ) ) {
			$arr = [];
			foreach ($errors as $k => $v) {
				$arr[] = $v['message'];
			}
			$this->addFlash( 'curlErrors', join( ',', $arr ) );
		} else {
			$this->addFlash( 'notice', "Выбранный домен имеет - {$result} позицию" );
			$this->insertHistory( $data, $result );
		}
	}
}