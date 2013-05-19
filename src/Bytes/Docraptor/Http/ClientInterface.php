<?php

namespace Bytes\Docraptor\Http;

interface ClientInterface
{
	function setBaseUrl($url);
	function setDefaultHeaders($headers);
	function post();
}