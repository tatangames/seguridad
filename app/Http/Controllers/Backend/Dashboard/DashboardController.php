<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Libros;
use App\Models\NichoCobros;
use App\Models\NichoMunicipal;
use App\Models\NichoMunicipalDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function vistaDashboard()
   {
       return view('backend.admin.dashboard.vistadashboard');
   }
}
