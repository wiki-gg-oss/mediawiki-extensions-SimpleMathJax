<?php
use MediaWiki\Html\Html;
class SimpleMathJaxHooks {

	public static function onParserFirstCallInit( Parser $parser ) {
		global $wgOut, $wgSmjUseChem;

		$wgOut->addModules( [ 'ext.SimpleMathJax' ] );

		$parser->setHook( 'math', __CLASS__ . '::renderMath' );
		if( $wgSmjUseChem ) $parser->setHook( 'chem', __CLASS__ . '::renderChem' );	}

	public static function renderMath($tex, array $args, Parser $parser, PPFrame $frame ) {
		global $wgSmjWrapDisplaystyle;
		$tex = str_replace('\>', '\;', $tex);
		$tex = str_replace('<', '\lt ', $tex);
		$tex = str_replace('>', '\gt ', $tex);
		if( $wgSmjWrapDisplaystyle ) $tex = "\displaystyle{ $tex }";
		return self::renderTex($tex, $parser);
	}

	public static function renderChem($tex, array $args, Parser $parser, PPFrame $frame ) {
		return self::renderTex("\ce{ $tex }", $parser);
	}

	private static function renderTex($tex, $parser) {
		$hookContainer = MediaWiki\MediaWikiServices::getInstance()->getHookContainer();
		$attributes = [ "style" => "opacity:.5", "class" => "smj-container" ];
		$hookContainer->run( "SimpleMathJaxAttributes", [ &$attributes, $tex ] );
		$element = Html::Element( "span", $attributes, "[math]{$tex}[/math]" );
		return [$element, 'markerType'=>'nowiki'];
	}
}
