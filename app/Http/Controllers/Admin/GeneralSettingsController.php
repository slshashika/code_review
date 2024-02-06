<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Models\Image;
use App\Models\Country;
use App\Models\Permission;
use App\Models\SiteSetting;
use App\Models\EmailSetting;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\ShippingCharge;
use App\Models\SidebarSetting;
use App\Models\MeasurementUnit;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class GeneralSettingsController extends Controller
{
    public function uploadSliderImagesUI()
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $sliderImages = Image::where('type', Image::SLIDER)->get();

            $sliderHeading = GeneralSetting::where('setting_key', 'slider_heading')->get()->first();
            $sliderDescription = GeneralSetting::where('setting_key', 'slider_description')->get()->first();

            return view('admin.settings.slider_settings', compact('sliderImages', 'sliderHeading', 'sliderDescription'));
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function uploadSliderImages(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            try {

                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images/uploads/slider/'), $imageName);
                $imageUrl = 'images/uploads/slider/' . $imageName;

                $newImage = new Image;

                $newImage->type = Image::SLIDER;
                $newImage->src = $imageUrl;
                $newImage->alt_text = $request->alt_text;
                $newImage->status = $request->status;
                $newImage->heading = $request->heading;
                $newImage->caption = $request->caption;

                $newImage = Image::create($newImage->toArray());

                return back()->with('success', 'New image slider added successfully !');
            } catch (\Exception $exception) {

                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function updateSliderImages(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            try {


                $updateImage = Image::find($request->image_id);

                if ($updateImage != null) {


                    if ($request->image != null) {

                        $imageName = time() . '.' . $request->image->extension();
                        $request->image->move(public_path('images/uploads/slider/'), $imageName);
                        $imageUrl = 'images/uploads/slider/' . $imageName;

                        $updateImage->src = $imageUrl;
                    }


                    $updateImage->alt_text = $request->alt_text;
                    $updateImage->status = $request->status;
                    $updateImage->heading = $request->heading;
                    $updateImage->caption = $request->caption;

                    $updateImage->save();

                    return back()->with('success', 'New image slider updated successfully !');
                } else {

                    return back()->with('error', 'Could not find the slider image');
                }
            } catch (\Exception $exception) {

                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    // Remove slider
    public function removeSliderImages($id)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            try {

                $imageDeleted = Image::where('id', $id)->delete();

                return back()->with('success', 'Image slider deleted successfully !');
            } catch (\Exception $exception) {

                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    //  load banner UI
    public function uploadBannerUI()
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $bannerImg = Image::where('type', 2)->where('entity', 'banner')->first();

            return view('admin.settings.banner_setting', compact('bannerImg'));
        } else {

            return redirect('admin/not_allowed');
        }
    }

    // Update the banner image
    public function updateBannerImage(Request $request)
    {

        $validated = $request->validate(
            [
                'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg',
            ],
            [
                'image.mimes' => 'Image types should be jpg,png,jpeg.',
            ]
        );

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            try {

                $updateImage = Image::find($request->banner_id);

                if ($updateImage != null) {


                    if ($request->image != null) {

                        $imageName = time() . '.' . $request->image->extension();
                        $request->image->move(public_path('images/uploads/banner/'), $imageName);
                        $imageUrl = 'images/uploads/banner/' . $imageName;

                        $updateImage->src = $imageUrl;
                    }

                    $updateImage->status = $request->status;

                    $updateImage->save();

                    return back()->with('success', 'Banner Image updated successfully !');

                } else {

                    return back()->with('error', 'Could not find the slider image');
                }
            } catch (\Exception $exception) {

                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    // Load the analytic UI
    public function updateAnalyticsUI(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $analytics = GeneralSetting::getSettingByKey('analytics');

            return view('admin.settings.analytics_settings', compact('analytics'));
        } else {

            return redirect('admin/not_allowed');
        }
    }

    // Update the google analytic code
    public function updateAnalytics(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            try {

                $analytics = GeneralSetting::getSettingByKey('analytics');

                if ($analytics != null) {

                    $analytics->description = $request->analytics;
                    $analytics->save();

                    return back()->with('success', 'Analytics code updated successfully !');
                } else {

                    return back()->with('error', 'Analytics configuration record not found.');
                }
            } catch (\Exception $exception) {

                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    //  Load site settings UI
    public function siteSettingsUI()
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $siteSettings = SiteSetting::with('templates')->get()->keyBy('section');

            $siteName = GeneralSetting::getSettingByKey('site_name');
            $siteDescription = GeneralSetting::getSettingByKey('site_description');
            $siteLogo = GeneralSetting::getSettingByKey('site_logo');

            $facebook_link = GeneralSetting::getSettingByKey('facebook_link');
            $instagram_link = GeneralSetting::getSettingByKey('instagram_link');
            $twitter_link = GeneralSetting::getSettingByKey('twitter_link');
            $youtube_link = GeneralSetting::getSettingByKey('youtube_link');

            $currencySymbol = GeneralSetting::getSettingByKey('currency_symbol');
            $freeShipping = GeneralSetting::getSettingByKey('free_shipping');
            $couponsEnabled = GeneralSetting::getSettingByKey('coupons_enabled');
            $lowStockMargin = GeneralSetting::getSettingByKey('low_stock_margin');
            $adminEmail = GeneralSetting::getSettingByKey('admin_email');

            $flatRate = ShippingCharge::loadShippingChargeMethods();

            return view('admin.settings.site_settings', compact('siteSettings', 'siteName', 'siteDescription', 'siteLogo', 'facebook_link', 'instagram_link', 'twitter_link', 'youtube_link', 'currencySymbol', 'freeShipping', 'couponsEnabled', 'lowStockMargin', 'adminEmail', 'flatRate'));
        } else {

            return redirect('admin/not_allowed');
        }
    }

    // Update site basic data
    public function updateSiteParameters(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $siteName = GeneralSetting::getSettingByKey('site_name');
            $siteName->description = $request->site_name;
            $siteName->save();

            $siteDescription = GeneralSetting::getSettingByKey('site_description');
            $siteDescription->description = $request->site_description;
            $siteDescription->save();

            if ($request->site_logo != null) {

                $siteLogo = GeneralSetting::getSettingByKey('site_logo');

                $imageName = time() . '.' . $request->site_logo->extension();
                $request->site_logo->move(public_path('images/uploads/logo/'), $imageName);
                $imageUrl = 'images/uploads/logo/' . $imageName;

                $siteLogo->description = $imageUrl;

                $siteLogo->save();
            }


            return back()->with('success', 'Site parameters updated successfully !');
        } else {

            return redirect('admin/not_allowed');
        }
    }


    public function updateSiteSettings(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            SiteSetting::updateSiteSetting('header_template', $request->header_template);
            SiteSetting::updateSiteSetting('slider_template', $request->slider_template);
            SiteSetting::updateSiteSetting('section1_template', $request->section1_template);
            SiteSetting::updateSiteSetting('section2_template', $request->section2_template);
            SiteSetting::updateSiteSetting('section3_template', $request->section3_template);
            SiteSetting::updateSiteSetting('footer_template', $request->footer_template);
            SiteSetting::updateSiteSetting('category_view_template', $request->category_view_template);
            SiteSetting::updateSiteSetting('post_card_template', $request->post_card_template);

            return back()->with('success', 'Site settings updated successfully !');
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function getAllTemplates(Request $request)
    {
        $searchKey = $request->searchKey;

        $templates = SiteTemplate::getTemplateForFilters($searchKey);
        $siteSettings = SiteSetting::with('templates')->get()->keyBy('section');

        return view('admin.settings.site_templates', compact('templates', 'siteSettings', 'searchKey'));
    }

    //  Add new Template
    public function addNewTemplate(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $validated = $request->validate(
                [

                    'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048|dimensions:min_width=50,min_height=50,max_width=50,max_height=50',

                ],
                [
                    'image.required' => 'Category image required.',
                    'image.mimes' => 'Image types should be jpg,png,jpeg.',
                    'image.dimensions' => 'Please upload the images with the mentioned image dimentions.',

                ]
            );


            $template = new SiteTemplate;

            $template->section = $request->section;
            $template->template_number = $request->template_number;



            if ($request->file('image')) {

                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images/uploads/template-images/'), $imageName);
                $imageUrl = 'images/uploads/template-images/' . $imageName;

                $template->template_image = $imageUrl;
            }



            SiteTemplate::create($template->toArray());

            return back()->with('success', 'Site template added successfully !');
        } else {

            return redirect('admin/not_allowed');
        }
    }


    public function updateTemplate(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {


            $validated = $request->validate(
                [

                    'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048|dimensions:min_width=50,min_height=50,max_width=2000,max_height=2000',

                ],
                [
                    'image.required' => 'Template image required.',
                    'image.mimes' => 'Image types should be jpg,png,jpeg.',
                    'image.dimensions' => 'Please upload the images with the mentioned image dimentions.',

                ]
            );



            $template = SiteTemplate::find($request->template_id);

            if ($template != null) {

                $template->section = $request->section;
                $template->template_number = $request->template_number;

                if ($request->file('image')) {

                    $imageName = time() . '.' . $request->image->extension();
                    $request->image->move(public_path('images/uploads/template-images/'), $imageName);
                    $imageUrl = 'images/uploads/template-images/' . $imageName;

                    $template->template_image = $imageUrl;
                }



                $template->save();

                return back()->with('success', 'Site template updated successfully !');
            } else {

                return back()->with('error', 'Could not find the site template');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function removeTemplate(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $template = SiteTemplate::where('id', $request->template_id)->get()->first();

            $activeTemplate = SiteSetting::where('section', 'header_template')->get()->first();


            if ($template != null) {


                if ($template->template_number == $activeTemplate->template_number) {

                    return back()->with('warning', 'The template you are trying to remove is an active tempate. Please set an different template as active and then try again.');
                } else {

                    $template = SiteTemplate::where('id', $request->template_id)->delete();

                    return back()->with('success', 'Site template removed successfully !');
                }
            } else {

                return back()->with('error', 'Could not find the site template');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    // Email SMTP config
    public function emailSettings(Request $request)
    {

        $emailSettings = EmailSetting::paginate(env("RECORDS_PER_PAGE"));

        return view('admin.settings.email_settings', compact('emailSettings'));
    }

    public function removeEmailConfig(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $emailSetting = EmailSetting::where('id', $request->email_id)->get()->first();


            if ($emailSetting != null) {

                $emailSetting = EmailSetting::where('id', $request->email_id)->delete();

                return back()->with('success', 'Email configuration removed successfully !');
            } else {

                return back()->with('error', 'Could not find the email configuration');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function addEmailConfig(Request $request)
    {


        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $this->validate(
                $request,
                [
                    'mailer' => 'required',
                    'host' => 'required',
                    'port' => 'required',
                    'username' => 'required',
                    'password' => 'required',
                    'encryption' => 'required',
                    'from_address' => 'required',
                    'from_name' => 'required',
                ],
                [
                    'mailer.required' => 'New password required.',
                    'host.required' => 'New password required.',
                    'port.required' => 'New password required.',
                    'username.required' => 'New password required.',
                    'password.required' => 'New password required.',
                    'encryption.required' => 'New password required.',
                    'from_address.required' => 'New password required.',
                    'from_name.required' => 'New password required.'

                ]
            );


            $emailSetting = EmailSetting::where('username', $request->username)->get()->first();


            if ($emailSetting == null) {

                $emailSetting = new EmailSetting;

                $emailSetting->fill($request->all());

                EmailSetting::create($request->toArray());

                return back()->with('success', 'Email configuration added successfully !');
            } else {

                return back()->with('error', 'Email configuration exists for the username - ' . $request->username);
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function updateEmailConfig(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $this->validate(
                $request,
                [
                    'mailer' => 'required',
                    'host' => 'required',
                    'port' => 'required',
                    'username' => 'required',
                    'password' => 'required',
                    'encryption' => 'required',
                    'from_address' => 'required',
                    'from_name' => 'required',
                ],
                [
                    'mailer.required' => 'New password required.',
                    'host.required' => 'New password required.',
                    'port.required' => 'New password required.',
                    'username.required' => 'New password required.',
                    'password.required' => 'New password required.',
                    'encryption.required' => 'New password required.',
                    'from_address.required' => 'New password required.',
                    'from_name.required' => 'New password required.'

                ]
            );

            
            $emailSetting = EmailSetting::find($request->email_id);


            if ($emailSetting != null) {

                $emailSetting->fill($request->all());

                $emailSetting->save();

                return back()->with('success', 'Email configuration updated successfully !');
            } else {

                return back()->with('error', 'Could not find the email configuration.');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function getTemplateForTemplateNumber(Request $request)
    {

        $templateNumber = $request->templateNumber;
        $section = $request->section;

        $template = SiteTemplate::where('template_number', $templateNumber)
            ->where('section', $section)->get()->first();


        if ($template != null) {

            return response()->json([
                'status' => true,
                'template' => $template
            ]);
        } else {
            return response()->json([
                'status' => false,
                'template' => null
            ]);
        }
    }

    public function getAllActiveTemplates()
    {

        $siteSettings = SiteSetting::all();


        $templates = array();

        foreach ($siteSettings as $siteSetting) {

            $template = SiteTemplate::where('template_number', $siteSetting->template_number)
                ->where('section', $siteSetting->section)->get()->first();

            if ($template != null) {
                array_push($templates, $template);
            }
        }

        return response()->json([
            'status' => true,
            'templates' => $templates
        ]);
    }

    public function updateSocialLinks(Request $request)
    {


        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            if ($request->facebook_link != null) {

                $facebook_link = GeneralSetting::getSettingByKey('facebook_link');

                $facebook_link->description = $request->facebook_link;
                $facebook_link->save();
            }

            if ($request->twitter_link != null) {

                $twitter_link = GeneralSetting::getSettingByKey('twitter_link');


                $twitter_link->description = $request->twitter_link;
                $twitter_link->save();
            }

            if ($request->instagram_link != null) {

                $instagram_link = GeneralSetting::getSettingByKey('instagram_link');


                $instagram_link->description = $request->instagram_link;
                $instagram_link->save();
            }

            if ($request->youtube_link != null) {

                $youtube_link = GeneralSetting::getSettingByKey('youtube_link');


                $youtube_link->description = $request->youtube_link;
                $youtube_link->save();
            }


            return back()->with('success', 'Social links updated successfully !');
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function updateSiteRobotsText(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $this->validate($request, ['robots_text' => 'required|mimes:txt']);

            if ($request->file()) {
                // upload file section;
                if ($request->file('robots_text')) {

                    $request->file('robots_text')->move(public_path(), "robots.txt");
                }

                return back()->with('success', 'File updated successfully !');
            } else {
                return back()->with('error', 'Please upload a file to continue');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function updateSiteMap(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $this->validate($request, ['sitemap' => 'required|mimes:xml']);

            if ($request->file()) {
                // upload file section;
                if ($request->file('sitemap')) {

                    $request->file('sitemap')->move(public_path(), "sitemap.xml");
                }

                return back()->with('success', 'File updated successfully !');
            } else {
                return back()->with('error', 'Please upload a file to continue');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }


    public function downloadFile($fileName)
    {
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/" . $fileName;



        if (file_exists($file)) {

            $headers = array(
                'Content-Type: application/pdf',
            );

            return Response::download($file, $fileName, $headers);
        } else {

            return back()->with('error', 'Sorry. The file does not exists.');
        }
    }

    public function getUnitsPage(Request $request)
    {

        $units_details = MeasurementUnit::paginate(env("RECORDS_PER_PAGE"));
        return view('admin.settings.units', compact('units_details'));
    }

    public function addUnits(Request $request)
    {

        $request->validate(['type' => 'required', 'symbol' => 'required', 'description' => 'required']);
        MeasurementUnit::create($request->all());
        return back()->with('success', 'Unit Submitted');
    }

    public function updateUnit(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $unit = MeasurementUnit::find($request->unit_id);

            if ($unit != null) {

                $unit->type = $request->type;
                $unit->symbol = $request->symbol;
                $unit->description = $request->description;

                $unit->save();

                return back()->with('success', 'Unit updated successfully !');
            } else {

                return back()->with('error', 'Could not find the Unit');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }



    public function removeUnit(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $unit = MeasurementUnit::where('id', $request->unit_id)->get()->first();




            if ($unit != null) {

                $unit = MeasurementUnit::where('id', $request->unit_id)->delete();

                return back()->with('success', 'Site Unit removed successfully !');
            } else {

                return back()->with('error', 'Could not find the site Unit');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }



    // Country Settings



    public function countrySettings(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $searchKey = $request->searchKey;
            $country_details = Country::getCountryForFilters($searchKey);


            return view('admin.settings.countries', compact('country_details', 'searchKey'));
        } else {

            return redirect('admin/not_allowed');
        }
    }


    public function addCountry(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            try {

                $newCountry = new Permission();
                $newCountry->country_code = $request->country_code;
                $newCountry->country_name = $request->country_name;
                $newCountry->dial_code = $request->dial_code;
                $newCountry->status = $request->status;

                Country::create($newCountry->toArray());

                return back()->with('success', 'Country created successfully !');
            } catch (\Exception $exception) {

                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }


    public function updateCountry(Request $request)
    {
        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $updateCountry = Country::find($request->country_id);

            if ($updateCountry != null) {


                $updateCountry->country_code = $request->country_code;
                $updateCountry->country_name = $request->country_name;
                $updateCountry->dial_code = $request->dial_code;
                $updateCountry->status = $request->status;
                $updateCountry->save();

                return back()->with('success', 'Country updated successfully !');
            } else {
                return back()
                    ->with('error', 'Could not find the Country');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }


    public function removeCountry(Request $request)
    {
        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $country = Country::find($request->country_id)->get()->first();

            if ($country != null) {
                $country = Country::where('id', $request->country_id)->delete();

                return back()->with('success', 'Country removed successfully !');
            } else {
                return back()
                    ->with('error', 'Could not find the site Country');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }


    public function checkCountryName(Request $request)
    {

        $name = $request->countryName;
        $id = $request->countryId;
        $country = Country::where('country_name', $name)->get()->first();


        if ($country == null) {

            return array(
                'status' => true,
                'message' => 'Can use the name'
            );
        } else {

            if ($country->id == $id) {
                return array(
                    'status' => true,
                    'message' => 'Can use the name'
                );
            } else {

                return array(
                    'status' => false,
                    'message' => 'Country exists for the name. Please use a different name',
                    'coupon' => $country
                );
            }
        }
    }

    public function sidebarSettingsUI(Request $request)
    {

        $sidebarSettings = SidebarSetting::get()->first();
        return view('admin.settings.sidebar_settings', compact('sidebarSettings'));
    }

    public function sidebarSettings(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {

            $sidebarSettings = SidebarSetting::get()->first();

            $sidebarSettings->users = $request->users != null ? 1 : 0;
            $sidebarSettings->products = $request->products != null ? 1 : 0;
            $sidebarSettings->inventory = $request->inventory != null ? 1 : 0;
            $sidebarSettings->categories = $request->categories != null ? 1 : 0;

            $sidebarSettings->tags = $request->tags != null ? 1 : 0;
            $sidebarSettings->all_orders = $request->all_orders != null ? 1 : 0;
            $sidebarSettings->posts = $request->posts != null ? 1 : 0;
            $sidebarSettings->marketing = $request->marketing != null ? 1 : 0;

            $sidebarSettings->web_pages = $request->web_pages != null ? 1 : 0;
            $sidebarSettings->zones = $request->zones != null ? 1 : 0;
            $sidebarSettings->promotions = $request->promotions != null ? 1 : 0;
            $sidebarSettings->advertisement = $request->advertisement != null ? 1 : 0;
            $sidebarSettings->inquiries = $request->inquiries != null ? 1 : 0;

            $sidebarSettings->save();

            return back()->with('success', 'Sidebar settings updated successfully !');
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function updateConfiguration(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('site_settings');

        if ($hasPermission) {


            // update currency symbol option
            if ($request->currency_symbol != null) {
                $currencySymbol = GeneralSetting::getSettingByKey('currency_symbol');
                if ($currencySymbol != null) {
                    $currencySymbol->description = $request->currency_symbol;
                    $currencySymbol->save();
                } else {
                    $currencySymbol = new GeneralSetting;
                    $currencySymbol->setting_key = 'currency_symbol';
                    $currencySymbol->description = $request->currency_symbol;

                    GeneralSetting::create($currencySymbol->toArray());
                }
            }

            // update free shipping option


            if ($request->free_shipping != null) {
                $freeShipping = GeneralSetting::getSettingByKey('free_shipping');
                if ($freeShipping != null) {

                    $freeShipping->description =  $request->free_shipping;;
                    $freeShipping->save();
                } else {
                    $freeShipping = new GeneralSetting;
                    $freeShipping->setting_key = 'free_shipping';
                    $freeShipping->description = $request->free_shipping;;
                    GeneralSetting::create($freeShipping->toArray());
                }
            } else {
                $freeShipping = GeneralSetting::getSettingByKey('free_shipping');

                if ($freeShipping != null) {

                    $freeShipping->description =  $request->free_shipping;;
                    $freeShipping->save();
                } else {
                    $freeShipping = new GeneralSetting;
                    $freeShipping->setting_key = 'free_shipping';
                    $freeShipping->description = 0;
                    GeneralSetting::create($freeShipping->toArray());
                }
            }

            // update coupons enabled option
            if ($request->coupons_enabled != null) {
                $couponsEnabled = GeneralSetting::getSettingByKey('coupons_enabled');
                if ($couponsEnabled != null) {

                    $couponsEnabled->description = 1;
                    $couponsEnabled->save();
                } else {
                    $couponsEnabled = new GeneralSetting;
                    $couponsEnabled->setting_key = 'coupons_enabled';
                    $couponsEnabled->description = 1;
                    GeneralSetting::create($couponsEnabled->toArray());
                }
            } else {
                $couponsEnabled = GeneralSetting::getSettingByKey('coupons_enabled');

                if ($couponsEnabled != null) {

                    $couponsEnabled->description = 0;
                    $couponsEnabled->save();
                } else {
                    $couponsEnabled = new GeneralSetting;
                    $couponsEnabled->setting_key = 'coupons_enabled';
                    $couponsEnabled->description = 0;
                    GeneralSetting::create($couponsEnabled->toArray());
                }
            }

            // update low stock margin
            if ($request->low_stock_margin != null) {
                $lowStockMargin = GeneralSetting::getSettingByKey('low_stock_margin');
                if ($lowStockMargin != null) {
                    $lowStockMargin->description = $request->low_stock_margin;
                    $lowStockMargin->save();
                } else {
                    $lowStockMargin = new GeneralSetting;
                    $lowStockMargin->setting_key = 'low_stock_margin';
                    $lowStockMargin->description = $request->low_stock_margin;

                    GeneralSetting::create($lowStockMargin->toArray());
                }
            }

            // update admin email
            if ($request->admin_email != null) {
                $adminEmail = GeneralSetting::getSettingByKey('admin_email');
                if ($adminEmail != null) {
                    $adminEmail->description = $request->admin_email;
                    $adminEmail->save();
                } else {
                    $adminEmail = new GeneralSetting;
                    $adminEmail->setting_key = 'admin_email';
                    $adminEmail->description = $request->admin_email;

                    GeneralSetting::create($adminEmail->toArray());
                }
            }


            return back()->with('success', 'Configuration settings updated successfully !');
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function updateFlatRate(Request $request)
    {

        $request->validate([
            'shipping_charges_type' => 'required',
            'weight_margin' => 'required | numeric',
            'weight_margin_cost' => 'required | numeric'
        ]);

        $flatRate = ShippingCharge::where('id', $request->id)->first();
        if ($flatRate) {
            $flatRate->shipping_charges_type = $request->shipping_charges_type;
            $flatRate->weight_margin = $request->weight_margin;
            $flatRate->weight_margin_cost = $request->weight_margin_cost;
            $flatRate->additional_weight_cost = $request->additional_weight_cost;
            $flatRate->save();
            return back()->with('success', 'Flat Rate updated successfully !');
        }
    }
}
