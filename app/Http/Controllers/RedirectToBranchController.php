<?php

namespace App\Http\Controllers;

use App\Models\Option;

class RedirectToBranchController
{
    public function __invoke($subdomain)
    {
        $branches = Option::where('option_key', 'institute.branches.json')->value('option_value');
        $branches = $branches ? json_decode($branches, true) : [];

        $branch = collect($branches)->firstWhere('sub_domain', $subdomain);

        if (! $branch) {
            abort(404);
        }

        $rootDomain = $branch['root_domain'] ?? 'hostdomain.com';

        return redirect()->away("https://{$subdomain}.{$rootDomain}");
    }
}
