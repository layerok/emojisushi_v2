<?php namespace Layerok\TgMall\Commands;

use Layerok\TgMall\Traits\Warn;
use OFFLINE\Mall\Models\Customer;

class BranchCommand extends LayerokCommand
{
    use Warn;
    protected $name = "branch";
    protected $description = "Use this command to get information about current restaurant";
    protected $pattern = "{type}";
    protected $types = ["phones", "delivery", "website"];

    protected function validate(): bool
    {
        if (!in_array($this->arguments['type'], $this->types)) {
            $this->warn(
                "[type] argument is required for [/branch] command. " .
                "Provide one of this types: " . implode(', ', $this->types) .
                " as a first argument"
            );
            return false;
        }
        return true;
    }

    public function handle()
    {
        if (!$this->validate()) {
            return;
        }
        parent::before();

        $method = $this->arguments['type'];
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    protected function phones()
    {
        $branch = $this->customer->branch;

        $phones = explode(',', $branch->phones);
        foreach ($phones as $phone) {
            $this->replyWithMessage([
                'text' => trim($phone)
            ]);
        }
    }

    protected function delivery()
    {

    }

    protected function website()
    {
        $this->replyWithMessage([
            'text' => 'https://emojisushi.com.ua'
        ]);
    }
}
