<?php
declare(strict_types=1);

namespace App\Tool;
use App\Tool\SuperglobalManager;

class Token
{
	public function __construct() {
		$this->superglobalManager = new SuperglobalManager();
        return $this;
	}
    public function generateString(): string 
    {
        $this->superglobalManager->setVariable('session', 'token', bin2hex(random_bytes(32)));
        return $this->superglobalManager->findVariable('session', 'token');
    }
    public function check(): bool
    {
        $postToken = $this->superglobalManager->findVariable('post', 'token');
        $sessionToken = $this->superglobalManager->findVariable('session', 'token');
        return ($postToken === $sessionToken && !is_null($postToken));
    }	
}
