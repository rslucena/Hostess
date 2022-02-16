<?php

declare(strict_types=1);

namespace app\Models;

class UserEntity
{
    public array $props = [];

    /**
     * Initializes building a user
     * userEntity constructor.
     *
     * @param $props
     */
    public function __construct($props)
    {
        $this->props = $props;

        //Rebuild the access
        $this->has_access();

        //Rebuild the avatar
        $this->has_avatar();

        //Build the reference (url)
        $this->has_reference();
    }

    /**
     * Updates the user's
     * access type in real time
     *
     * @return void
     */
    private function has_access(): void
    {
        if (empty($this->props['parceiro_id'])) {
            AuthProvider::updateSession('config', 'experiences', 0);
        } else {
            AuthProvider::updateSession('config', 'experiences', $this->props['parceiro_id']);
        }
    }

    /**
     * Checks if there is an avatar
     * if not, generate the thumb automatically
     *
     * @return string
     */
    private function has_avatar(): string
    {
        $size = '37x37';
        $url = 'https://via.placeholder.com/';

        if (empty($this->props['avatar'])) {
            $this->props['avatar'] = $url . $size;
        }

        $this->props['avatar'] = APP_API . $this->props['avatar'];

        return $this->props['avatar'];
    }

    /**
     * Creates a reference link
     * to the profile page
     *
     * @return string
     */
    private function has_reference(): string
    {
        $name = $this->props['nome'];

        if (empty($this->props['email'])) {
            $name = explode("@", $this->props['email']);
            $name = $name[0];
        }

        return $this->props['reference'] = $this->slug($name);
    }

    /**
     * Returns when requesting
     * user data
     *
     * @return array
     */
    public function build(): array
    {
        return $this->props;
    }

    /**
     * Clear user created
     * data from session
     */
    public function __destruct()
    {
        $this->props = [];
    }
}
