<?php
// namespace administrator\components\com_jrealtimeanalytics\framework\http\transport;
/**
 * @package JREALTIMEANALYTICS::FRAMEWORK::administrator::components::com_jrealtimeanalytics
 * @subpackage framework
 * @subpackage http
 * @subpackage transport
 * @author Joomla! Extensions Store
 * @copyright (C) 2013 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

/**
 * HTTP connector client transport object
 * 
 * @package JREALTIMEANALYTICS::FRAMEWORK::administrator::components::com_jrealtimeanalytics
 * @subpackage framework
 * @subpackage http
 * @subpackage transport
 * @since 2.0
 */
class JRealtimeHttpTransportSocket implements JRealtimeHttpTransport {
	/**
	 * @var    array  Reusable socket connections.
	 * @since 2.0
	 */
	protected $connections;

	/**
	 * @var    JRegistry  The client options.
	 * @since 2.0
	 */
	protected $options;

	/**
	 * Constructor.
	 *  
	 * @since 2.0
	 * @throws  JRealtimeExceptionRuntime
	 */
	public function __construct() {
		if (!function_exists('fsockopen') || !is_callable('fsockopen')) {
			throw new JRealtimeExceptionRuntime('Cannot use a socket transport when fsockopen() is not available.', 'error');
		}

	}

	/**
	 * Send a request to the server and return a JRealtimeHttpResponse object with the response.
	 *
	 * @param   string   $method     The HTTP method for sending the request.
	 * @param   JUri     $uri        The URI to the resource to request.
	 * @param   mixed    $data       Either an associative array or a string to be sent with the request.
	 * @param   array    $headers    An array of request headers to send with the request.
	 * @param   integer  $timeout    Read timeout in seconds.
	 * @param   string   $userAgent  The optional user agent string to send with the request.
	 *
	 * @return  JRealtimeHttpResponse
	 *
	 * @since 2.0
	 * @throws  JRealtimeExceptionRuntimes
	 */
	public function request($method, JUri $uri, $data = null, array $headers = null, $timeout = 25, $userAgent = null) {
		$connection = $this->connect($uri, $timeout);

		// Make sure the connection is alive and valid.
		if (is_resource($connection)) {
			// Make sure the connection has not timed out.
			$meta = stream_get_meta_data($connection);
			if ($meta['timed_out']) {
				throw new JRealtimeExceptionRuntime('Server connection timed out.', 'error');
			}
		} else {
			throw new JRealtimeExceptionRuntime('Not connected to server.', 'error');
		}

		// Get the request path from the URI object.
		$path = $uri->toString(array('path', 'query'));

		// If we have data to send make sure our request is setup for it.
		if (!empty($data)) {
			// If the data is not a scalar value encode it to be sent with the request.
			if (!is_scalar($data)) {
				$data = http_build_query($data);
			}

			// Add the relevant headers.
			$headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
			$headers['Content-Length'] = strlen($data);
		}

		// Build the request payload.
		$request = array();
		$request[] = strtoupper($method) . ' ' . ((empty($path)) ? '/' : $path) . ' HTTP/1.0';
		$request[] = 'Host: ' . $uri->getHost();

		// If an explicit user agent is given use it.
		if (isset($userAgent)) {
			$headers['User-Agent'] = $userAgent;
		}

		// If there are custom headers to send add them to the request payload.
		if (is_array($headers)) {
			foreach ($headers as $k => $v) {
				$request[] = $k . ': ' . $v;
			}
		}

		// If we have data to send add it to the request payload.
		if (!empty($data)) {
			$request[] = null;
			$request[] = $data;
		}

		// Send the request to the server.
		fwrite($connection, implode("\r\n", $request) . "\r\n\r\n");

		// Get the response data from the server.
		$content = '';

		while (!feof($connection)) {
			$content .= fgets($connection, 4096);
		}

		fclose($connection);
		return $this->getResponse($content);
	}

	/**
	 * Method to get a response object from a server response.
	 *
	 * @param   string  $content  The complete server response, including headers.
	 *
	 * @return  JRealtimeHttpResponse
	 *
	 * @since 2.0
	 * @throws  JRealtimeExceptionUnexpectedvalue
	 */
	protected function getResponse($content) {
		// Create the response object.
		$return = new JRealtimeHttpResponse;

		// Split the response into headers and body.
		$response = explode("\r\n\r\n", $content, 2);

		// Get the response headers as an array.
		$headers = explode("\r\n", $response[0]);

		// Set the body for the response.
		$return->body = $response[1];

		// Get the response code from the first offset of the response headers.
		preg_match('/[0-9]{3}/', array_shift($headers), $matches);
		$code = $matches[0];
		if (is_numeric($code)) {
			$return->code = (int) $code;
		} else { // No valid response code was detected.
			throw new JRealtimeExceptionUnexpectedvalue('No HTTP response code found.', 'notice');
		}

		// Add the response headers to the response object.
		foreach ($headers as $header) {
			$pos = strpos($header, ':');
			$return->headers[trim(substr($header, 0, $pos))] = trim(substr($header, ($pos + 1)));
		}

		return $return;
	}

	/**
	 * Method to connect to a server and get the resource.
	 *
	 * @param   JUri     $uri      The URI to connect with.
	 * @param   integer  $timeout  Read timeout in seconds.
	 *
	 * @return  resource  Socket connection resource.
	 *
	 * @since 2.0
	 * @throws  JRealtimeExceptionRuntime
	 */
	protected function connect(JUri $uri, $timeout = null) {
		// Initialize variables.
		$errno = null;
		$err = null;

		// Get the host from the uri.
		$host = ($uri->isSSL()) ? 'ssl://' . $uri->getHost() : $uri->getHost();

		// If the port is not explicitly set in the URI detect it.
		if (!$uri->getPort()) {
			$port = ($uri->getScheme() == 'https') ? 443 : 80;
		}
		// Use the set port
 		else {
			$port = $uri->getPort();
		}

		// Build the connection key for resource memory caching.
		$key = md5($host . $port);

		// If the connection already exists, use it.
		if (!empty($this->connections[$key]) && is_resource($this->connections[$key])) {
			// Make sure the connection has not timed out.
			$meta = stream_get_meta_data($this->connections[$key]);
			if (!$meta['timed_out']) {
				return $this->connections[$key];
			}
		}

		// Attempt to connect to the server.
		$connection = @fsockopen($host, $port, $errno, $err, $timeout);
		if (!$connection) {
			throw new JRealtimeExceptionRuntime($err, 'error', $errno);
		}

		// since 2.0
		$this->connections[$key] = $connection;

		// If an explicit timeout is set, set it.
		if (isset($timeout)) {
			stream_set_timeout($this->connections[$key], (int) $timeout);
		}

		return $this->connections[$key];
	}
}
