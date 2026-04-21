<?php

namespace DOSpaces\Integration\Integrations;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Integration Interface
 * Contract for third-party plugin integrations
 */
interface IntegrationInterface {

    /**
     * Check if the target plugin is active
     *
     * @return bool
     */
    public function is_active(): bool;

    /**
     * Register hooks for the integration
     */
    public function init(): void;
}
