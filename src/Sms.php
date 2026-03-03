<?php

declare(strict_types=1);

namespace Winavin\Sms;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Sms
{
    protected ?string $mobile = null;

    protected ?string $text = null;

    protected ?string $channel = null;

    protected array $customConfig = [];

    /**
     * Set the mobile number.
     *
     * @return $this
     */
    public function to(string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Explicitly set the SMS channel to be used.
     *
     * @return $this
     */
    public function channel(?string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Set the SMS message text.
     *
     * @return $this
     */
    public function text(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Set custom configuration overrides.
     *
     * @param  array|string  $keyOrConfig
     * @param  mixed  $value
     * @return $this
     */
    public function setConfig($keyOrConfig, $value = null): self
    {
        if (is_array($keyOrConfig)) {
            $this->customConfig = array_merge($this->customConfig, $keyOrConfig);
        } else {
            $this->customConfig[$keyOrConfig] = $value;
        }

        return $this;
    }

    /**
     * Send the SMS message.
     *
     * When `send_sms_only_in_production` is true and the current environment
     * is not production, the HTTP request is skipped and the message is logged
     * instead.
     *
     * @return \Illuminate\Http\Client\Response
     *
     * @throws Exception
     */
    public function send($mobile = null, $text = null, $channel = null): \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
    {
        [$mobile, $text, $channel] = $this->resolveArguments($mobile, $text, $channel);

        $this->validateRequiredParameters($mobile, $text);

        $config = $this->buildConfiguration($channel);
        $this->validateConfiguration($config);

        $baseUrl = $config['base_url'];
        unset($config['base_url'], $config['send_sms_only_in_production']);

        $config = array_filter($config, fn ($value) => ! is_null($value));

        $payload = $this->buildPayload($mobile, $text, $config);

        if ($this->shouldRestrictToProduction() && ! app()->isProduction()) {
            $response = $this->performFakeRequest($mobile, $text, $payload);
        } else {
            $response = $this->performRequest($baseUrl, $payload);
        }

        $this->resetState();

        return $response;
    }

    /**
     * Resolve the arguments, falling back to class state.
     */
    protected function resolveArguments(?string $mobile, ?string $text, ?string $channel): array
    {
        return [
            $mobile ?? $this->mobile,
            $text ?? $this->text,
            $channel ?? $this->channel,
        ];
    }

    /**
     * Validate that required parameters are present.
     *
     * @throws Exception
     */
    protected function validateRequiredParameters(?string $mobile, ?string $text): void
    {
        if (empty($mobile) || empty($text)) {
            throw new Exception('Mobile number and text message are required to send an SMS.');
        }
    }

    /**
     * Build the configuration for the selected channel.
     *
     * @throws Exception
     */
    protected function buildConfiguration(?string $channel): array
    {
        $channelName = $channel ?? config('sms.default');
        $channelConfig = config("sms.channels.{$channelName}", []);

        if (empty($channelConfig)) {
            throw new Exception("SMS configuration is not defined for channel: {$channelName}");
        }

        return array_merge($channelConfig, $this->customConfig);
    }

    /**
     * Validate the merged configuration.
     *
     * @throws Exception
     */
    protected function validateConfiguration(array $config): void
    {
        if (empty($config['base_url'])) {
            throw new Exception('Base URL is missing from the SMS configuration.');
        }
    }

    /**
     * Determine if SMS sending should be restricted to production environments only.
     */
    protected function shouldRestrictToProduction(): bool
    {
        return (bool) ($this->customConfig['send_sms_only_in_production'] ?? config('sms.send_sms_only_in_production', false));
    }

    /**
     * Build the final payload array to be sent in the request.
     */
    protected function buildPayload(string $mobile, string $text, array $config): array
    {
        $payload = array_merge($config, [
            'number' => $mobile,
            'text' => $text,
        ]);

        return array_filter($payload, fn ($value) => ! is_null($value));
    }

    /**
     * Perform a fake request by logging and returning a successful response.
     */
    protected function performFakeRequest(string $mobile, string $text, array $payload): \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
    {
        Log::info('[SMS] Skipped – not in production.', [
            'to' => $mobile,
            'text' => $text,
            'payload' => $payload,
        ]);

        return Http::response('SMS skipped (non-production)', 200);
    }

    /**
     * Perform the actual HTTP request to the SMS gateway.
     */
    protected function performRequest(string $baseUrl, array $payload): \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
    {
        return Http::when(
            ! app()->isProduction(),
            fn ($http) => $http->withoutVerifying()
        )->get($baseUrl, $payload);
    }

    /**
     * Reset the internal state.
     */
    protected function resetState(): void
    {
        $this->mobile = null;
        $this->text = null;
        $this->channel = null;
        $this->customConfig = [];
    }
}
