<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Events\TerminusCommandExecuted;

class CreatePantheonSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requestData;

    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
    }

    public function handle()
    {
        $label = $this->requestData['site_label'];
        $machineName = strtolower($label);
        $machineName = str_replace(' ', '-', $machineName);
        $cms = $this->requestData['cms'];
        $supportingOrg = $this->requestData['supporting_org'];
        $sitePlan = $this->requestData['site_plan'];
        $tag = $this->requestData['tag'];

       //$machineToken = env('PANTHEON_MACHINE_TOKEN');
       //$organization = env('PANTHEON_ORGANIZATION');

       $machineToken = "8vnHInADKi6Tj0PbAUjeiqGDt8dhomi29Zwbj02oUGpxh";
       $organization = "tyler-technologies";

        $upstreamId = $this->getUpstreamId($cms);

        try {
            // Prepare a message to broadcast
            $message = "Commands executed successfully for site: $machineName with label: $label.";
            // Broadcast the event with the message
            broadcast(new TerminusCommandExecuted($message));
            //event(new TerminusCommandExecuted($message));

            $this->executeCommand("terminus auth:login --machine-token=$machineToken");
            $this->executeCommand("terminus site:create --org=$organization \"$machineName\" \"$label\" $upstreamId");
            $this->executeCommand("terminus site:org:add $machineName $supportingOrg");
            $this->executeCommand("terminus tag:add $machineName $organization $tag");

            // Retrieve site ID from the site info
            $siteInfoJson = $this->executeCommand("terminus site:info $machineName --fields=ID --format=json", true);
            $siteId = $this->extractSiteIdFromJson($siteInfoJson);

            // Set the site plan
            $this->executeCommand("terminus plan:set $siteId $sitePlan");

            \Log::info("Site creation successful: $machineName");
        } catch (\Exception $e) {
            \Log::error("Site creation failed for '$machineName': " . $e->getMessage());
            throw $e;
        }
    }

    private function getUpstreamId($cms)
    {
        $upstreamIds = [
            'drupal7' => '21e1fada-199c-492b-97bd-0b36b53a9da0',
            'drupal9' => '8a129104-9d37-4082-aaf8-e6f31154644e',
            'drupal10' => 'bde48795-b16d-443f-af01-8b1790caa1af',
            'wordpress' => 'e8fe8550-1ab9-4964-8838-2b9abdccf4bf',
        ];

        if (!array_key_exists($cms, $upstreamIds)) {
            throw new \Exception('Unsupported CMS selection');
        }

        return $upstreamIds[$cms];
    }

    private function executeCommand($command, $returnOutput = false)
    {
        \Log::info("Executing command: $command");

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(3600); // Set timeout to 1 hour if needed

        try {
            $process->mustRun();

            if ($returnOutput) {
                return trim($process->getOutput());
            }
        } catch (ProcessFailedException $exception) {
            \Log::error("Command '$command' failed: " . $exception->getMessage());
            throw new \Exception("Command '$command' failed: " . $exception->getMessage());
        }
    }

    private function extractSiteIdFromJson($json)
    {
        $data = json_decode($json, true);

        if (isset($data['id'])) {
            return $data['id'];
        }

        throw new \Exception('Site ID not found in JSON response');
    }
}
