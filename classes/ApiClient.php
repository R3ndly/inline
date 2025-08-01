<?php
namespace Inline;

class ApiClient {
	private $postsUrl;
	private $commentsUrl;

	public function __construct($postsUrl, $commentsUrl)
	{
		$this->postsUrl = $postsUrl;
		$this->commentsUrl = $commentsUrl;
	}

	public function fetchPosts() {
		$jsonResponse = file_get_contents($this->postsUrl);
		return json_decode($jsonResponse, true);
	}

	public function fetchComments() {
		$jsonResponse = file_get_contents($this->commentsUrl);
		return json_decode($jsonResponse, true);
	}
}
