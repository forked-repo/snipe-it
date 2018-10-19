<?php
namespace App\Models;

use App\Models\Loggable;
use App\Models\Traits\Acceptable;
use App\Notifications\CheckinLicenseNotification;
use App\Notifications\CheckoutLicenseNotification;
use App\Presenters\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LicenseSeat extends SnipeModel implements ICompanyableChild
{
    use CompanyableChildTrait;
    use SoftDeletes;
    use Loggable;

    protected $presenter = 'App\Presenters\LicenseSeatPresenter';
    use Presentable;

    protected $dates = ['deleted_at'];
    protected $guarded = 'id';
    protected $table = 'license_seats';

    use Acceptable;

    public function getCompanyableParents()
    {
        return ['asset', 'license'];
    }

    /**
     * Determine whether the user should be required to accept the license
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return boolean
     */
    public function requireAcceptance()
    {
        return $this->license->category->require_acceptance;
    }

    public function getEula() {
        return $this->license->getEula();
    }
    
    /**
     * Establishes the seat -> license relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function license()
    {
        return $this->belongsTo('\App\Models\License', 'license_id');
    }

    /**
     * Establishes the seat -> assignee relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v1.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function user()
    {
        return $this->belongsTo('\App\Models\User', 'assigned_to')->withTrashed();
    }

    /**
     * Establishes the seat -> asset relationship
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function asset()
    {
        return $this->belongsTo('\App\Models\Asset', 'asset_id')->withTrashed();
    }

    /**
     * Determines the assigned seat's location based on user
     * or asset its assigned to
     *
     * @author A. Gianotto <snipe@snipe.net>
     * @since [v4.0]
     * @return string
     */
    public function location()
    {
        if (($this->user) && ($this->user->location)) {
            return $this->user->location;

        } elseif (($this->asset) && ($this->asset->location)) {
            return $this->asset->location;
        }

        return false;

    }


}
