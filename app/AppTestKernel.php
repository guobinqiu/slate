<?php
require_once __DIR__.'/AppKernel.php';

class AppTestKernel extends AppKernel
{
    private $kernelModifier = null;

    public function boot()
    {
        parent::boot();

        if ($kernelModifier = $this->kernelModifier) {
            $kernelModifier($this);
            $this->kernelModifier = null;
        };
    }

    public function setKernelModifier(\Closure $kernelModifier)
    {
        $this->kernelModifier = $kernelModifier;

        // We force the kernel to shutdown to be sure the next request will boot it
        $this->shutdown();
    }

}
