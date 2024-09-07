<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareImagePostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareVideoPostInterface;

/**
 * Class FacebookService
 *
 * Service for managing and posting content to Facebook using the Graph API.
 *
 * Implements sharing of general posts, images, and videos to a Facebook page.
 */
class FacebookService extends SocialMediaService implements ShareInterface, ShareImagePostInterface, ShareVideoPostInterface {

    /**
     * @var string Facebook access token
     */
    private $access_token;

    /**
     * @var string Facebook page ID
     */
    private $page_id;

    /**
     * @var FacebookService|null Singleton instance
     */
    private static ?FacebookService $instance = null;
    /**
     * Facebook API version
     */
    private const API_VERSION = 'v20.0';


    /**
     * Private constructor to prevent direct instantiation.
     */

    private function __construct(string $accessToken, string $pageId) {
        $this->access_token = $accessToken;
        $this->page_id = $pageId;
    }

    /**
     * Get the singleton instance of FacebookService.
     *
     * @return FacebookService
     */
    public static function getInstance() {
        if (self::$instance === null) {
            $accessToken = config('autopost.facebook_access_token');
            $pageId = config('autopost.facebook_page_id');
            self::$instance = new self($accessToken, $pageId);
        }
        return self::$instance;
    }

    /**
     * Share an image post with a caption and an image URL to Facebook.
     *
     * @param string $caption The caption to accompany the image.
     * @param string $image_url The URL of the image.
     *
     * @return mixed Response from the Facebook API.
     */
    public function shareImage($caption, $image_url) {
        $url = $this->buildApiUrl('photos');
        $params = $this->buildParams([
            'url'     => $image_url,
            'caption' => $caption,
        ]);

        return $this->sendRequest($url, 'post', $params);
    }

    /**
     * Share a text post with a caption and a URL to Facebook.
     *
     * @param string $caption The caption to accompany the post.
     * @param string $url The URL to share.
     *
     * @return mixed Response from the Facebook API.
     */

    public function share($caption, $url) {
        $feedUrl = $this->buildApiUrl('feed');
        $params = $this->buildParams([
            'message' => $caption,
            'link'    => $url,
        ]);

        return $this->sendRequest($feedUrl, 'post', $params);
    }

    /**
     * Share a video post with a caption and a video URL to Facebook.
     *
     * @param string $caption The caption to accompany the video.
     * @param string $video_url The URL of the video (local file path or remote URL).
     *
     * @return mixed Response from the Facebook API.
     */
    public function shareVideo( $caption,  $video_url) {
        // Step 1: Check if the video URL is remote and download the file if necessary
        $video_path = $this->downloadIfRemote($video_url);

        if (!file_exists($video_path)) {
            return ['error' => 'Failed to download video or file does not exist.'];
        }

        // Step 2: Get the size of the video file
        $fileSize = filesize($video_path);

        // Step 3: Start the upload session
        $startUrl = $this->buildApiUrl('videos');
        $params = $this->buildParams([
            'upload_phase' => 'start',
            'file_size'    => $fileSize, // Total size of the video file
        ]);

        $response = $this->sendRequest($startUrl, 'post', $params);
        $uploadSessionId = $response['upload_session_id'] ?? null;

        if (!$uploadSessionId) {
            return ['error' => 'Failed to start video upload session.'];
        }

        // Step 4: Upload the video in chunks (if required)
        $startOffset = $response['start_offset'] ?? 0;
        $endOffset = $response['end_offset'] ?? $fileSize;

        while ($startOffset < $endOffset) {
            $chunkPath = $this->saveVideoChunk($video_path, $startOffset, $endOffset);

            // Ensure the chunk was saved successfully
            if (!file_exists($chunkPath)) {
                return ['error' => 'Failed to save video chunk.'];
            }

            // Transfer phase - upload the chunk
            $params = $this->buildParams([
                'upload_phase'      => 'transfer',
                'upload_session_id' => $uploadSessionId,
                'start_offset'      => $startOffset,
                'video_file_chunk'  => new \CURLFile($chunkPath) // Pass the chunk as a CURLFile
            ]);

            $transferResponse = $this->sendRequest($startUrl, 'post', $params);
            $startOffset = $transferResponse['start_offset'] ?? $endOffset;
            $endOffset = $transferResponse['end_offset'] ?? $fileSize;
        }

        // Step 5: Complete the video upload
        return $this->completeVideoUpload($uploadSessionId, $caption);
    }

    /**
     * Helper to download the video file if it's a remote URL.
     *
     * @param string $video_url The remote URL or local file path of the video.
     *
     * @return string Local file path of the video (either the original path or downloaded file).
     */
    private function downloadIfRemote(string $video_url): string {
        // Check if the URL is a remote URL
        if (filter_var($video_url, FILTER_VALIDATE_URL)) {
            // Download the remote file and save it locally
            $tempPath = sys_get_temp_dir() . '/' . basename($video_url);
            file_put_contents($tempPath, fopen($video_url, 'r'));
            return $tempPath; // Return the path to the downloaded file
        }

        // If it's already a local file, just return the same path
        return $video_url;
    }

    /**
     * Helper to save a chunk of the video file for transfer.
     *
     * @param string $video_path Path to the video file.
     * @param int $start_offset The start byte for the chunk.
     * @param int $end_offset The end byte for the chunk.
     *
     * @return string The path to the saved chunk file.
     */
    private function saveVideoChunk(string $video_path, int $start_offset, int $end_offset): string {
        $chunkPath = sys_get_temp_dir() . '/' . uniqid('video_chunk_') . '.mp4';

        $handle = fopen($video_path, 'rb');
        fseek($handle, $start_offset);
        $chunkData = fread($handle, $end_offset - $start_offset);
        fclose($handle);

        // Save the chunk data to a temporary file
        file_put_contents($chunkPath, $chunkData);

        return $chunkPath;
    }

    /**
     * Complete the video upload process.
     *
     * @param string $uploadSessionId The upload session ID.
     * @param string $caption The caption to accompany the video.
     *
     * @return mixed Response from the Facebook API.
     */
    private function completeVideoUpload(string $uploadSessionId, string $caption) {
        $completeUrl = $this->buildApiUrl('videos');
        $params = $this->buildParams([
            'upload_phase'      => 'finish',
            'upload_session_id' => $uploadSessionId,
            'description'       => $caption,
            'title'             => $caption,
            'published'         => true,
        ]);

        return $this->sendRequest($completeUrl, 'post', $params);
    }



    /**
     * Retrieve insights for the Facebook page.
     *
     * @return mixed Response from the Facebook API.
     */
    public function getPageInsights(array $metrics = [], array $additionalParams = []): array {
        $url = $this->buildApiUrl('insights');
        $params = $this->buildParams(array_merge([
            'metric' => implode(',', $metrics),
        ], $additionalParams));

        return $this->sendRequest($url, 'get', $params);
    }


    /**
     * Retrieve information about the Facebook page.
     *
     * @return mixed Response from the Facebook API.
     */
    public function getPageInfo() {
        $url = $this->buildApiUrl();
        $params = $this->buildParams();

        return $this->sendRequest($url, 'get', $params);
    }


    /**
     * Helper to build Facebook Graph API URL.
     *
     * @param string $endpoint
     *
     * @return string
     */
    private function buildApiUrl(string $endpoint = ''): string {
        $apiVersion = config('autopost.facebook_api_version');
        return 'https://graph.facebook.com/' . $apiVersion . '/' . $this->page_id . '/' . $endpoint;
    }

    /**
     * Helper to build request parameters.
     *
     * @param array $params
     *
     * @return array
     */
    private function buildParams(array $params = []): array {
        return array_merge($params, ['access_token' => $this->access_token]);
    }
}