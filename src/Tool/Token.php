<?php
declare(strict_types=1);

namespace App\Tool;

class Token
{
    private $superglobalManager;

	public function __construct() {
		$this->superglobalManager = new SuperglobalManager();
        return $this;
	}
    public function generateString(int $tokenNumber = 0): string 
    {
        $this->superglobalManager->setVariable('session', 'token' . $tokenNumber, bin2hex(random_bytes(32)));
        return $this->superglobalManager->findVariable('session', 'token' . $tokenNumber);
    }
    public function check(int $tokenNumber = 0, bool $delete = true): bool
    {
        $postToken = $this->superglobalManager->findVariable('post', 'token' . $tokenNumber);
        $sessionToken = $this->superglobalManager->findVariable('session', 'token' . $tokenNumber);
        if ($delete) {
            $this->superglobalManager->unsetVariable('session', 'token' . $tokenNumber);
        }
        return ($postToken === $sessionToken && !is_null($postToken));
    }	
}
