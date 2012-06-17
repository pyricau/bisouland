<?php

namespace Bisouland\GameSystemBundle\Kiss;

class Success
{
    private $isSuccess;
    private $isCritical;

    public function setIsSuccess($isSuccess)
    {
        $this->isSuccess = $isSuccess;
    }

    public function getIsSuccess()
    {
        return $this->isSuccess;
    }

    public function setIsCritical($isCritical)
    {
        $this->isCritical = $isCritical;
    }

    public function getIsCritical()
    {
        return $this->isCritical;
    }
}
