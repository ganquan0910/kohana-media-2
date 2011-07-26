<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Media extends Controller {

	protected $_config = 'media';

	public function before()
	{
		parent::before();

		// Attach configuration
		$this->_config = Kohana::$config->load($this->_config)->as_array();
	}

	public function action_index()
	{
		// Get environment
		$environment = $this->request->param('environment');

		// Get the file path from the request
		$file = $this->request->param('file');

		if ( ! isset($this->_config[$environment]))
		{
			return $this->response->status(404);
		}

		$config = $this->_config[$environment];

		$files = explode(Media::$delimiter, $file);

		if ( ! is_array($files))
		{
			$files = array($files);
		}

		$extension = pathinfo($file, PATHINFO_EXTENSION);

		// Improve file content protection
		if ((is_array($config['types']) AND ! in_array($extension, $config['types'])) OR
			( ! is_array($config['types']) AND $extension != $config['types']))
		{
			return $this->response->status(404);
		}

		$media = Media::instance($environment);

		// Set the proper headers to allow caching
		$this->response->headers('content-type', File::mime_by_ext($extension));

		// Send the file content as the response
		$this->response->body($media->minify_files($file));
	}

} // End Controller_Media