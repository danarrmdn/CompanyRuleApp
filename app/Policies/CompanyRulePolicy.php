<?php

namespace App\Policies;

use App\Models\CompanyRule;
use App\Models\User;

class CompanyRulePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CompanyRule $companyRule): bool
    {
        // If the document is approved or rejected, any logged-in user can view it.
        if (in_array($companyRule->status, ['Approved', 'Rejected'])) {
            return true;
        }

        // Otherwise, only the creator or assigned approvers can view it.
        if ($user->id === $companyRule->creator_id) {
            return true;
        }

        $approvers = [
            $companyRule->controller_1_id,
            $companyRule->controller_2_id,
            $companyRule->controller_3_id,
            $companyRule->controller_4_id,
            $companyRule->controller_5_id,
            $companyRule->approver_1_id,
            $companyRule->approver_2_id,
            $companyRule->approver_3_id,
        ];

        return in_array($user->id, array_filter($approvers));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CompanyRule $companyRule): bool
    {
        return $user->id === $companyRule->creator_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CompanyRule $companyRule): bool
    {
        return $user->id === $companyRule->creator_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CompanyRule $companyRule): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CompanyRule $companyRule): bool
    {
        return false;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, CompanyRule $companyRule): bool
    {
        if ($companyRule->status === 'Pending Approval 1') {
            return $user->id === $companyRule->controller_1_id;
        } elseif ($companyRule->status === 'Pending Approval 2') {
            return $user->id === $companyRule->controller_2_id;
        } elseif ($companyRule->status === 'Pending Approval 3') {
            return $user->id === $companyRule->controller_3_id;
        } elseif ($companyRule->status === 'Pending Approval 4') {
            return $user->id === $companyRule->controller_4_id;
        } elseif ($companyRule->status === 'Pending Approval 5') {
            return $user->id === $companyRule->controller_5_id;
        }

        return false;
    }
}
