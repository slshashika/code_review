<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use View;

use App\Models\GeneralSetting;
use App\Models\SiteSetting;
use App\Models\Image;
use App\Models\Page;
use App\Models\Category;
use App\Models\SidebarSetting;

class ContentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

     public $commonContent;
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        
            $sliderImages = Image::getSliderImages();
            $siteName = GeneralSetting::getSettingByKey('site_name');
            $siteDescription = GeneralSetting::getSettingByKey('site_description');
            $siteLogo = GeneralSetting::getSettingByKey('site_logo');
            $analytics = GeneralSetting::getSettingByKey('analytics');

            $currencySymbol = GeneralSetting::getSettingByKey('currency_symbol');

            $facebookLink = GeneralSetting::getSettingByKey('facebook_link');
            $twitterLink = GeneralSetting::getSettingByKey('instagram_link');
            $instagramLink = GeneralSetting::getSettingByKey('twitter_link');
            $youtubeLink = GeneralSetting::getSettingByKey('youtube_link');

            $couponsEnabled = GeneralSetting::getSettingByKey('coupons_enabled');


            $siteSettings = SiteSetting::all()->keyBy('section');

            $pages = Page::getAllVisiblePages();
            $categories = Category::with('subCategories')->where('type',Category::PRODUCT)->get();

            $sidebarSettings = SidebarSetting::get()->first();

            $content = [];

            $content['sliderImages'] = $sliderImages;
            $content['siteName'] = $siteName;
            $content['siteDescription'] = $siteDescription;
            $content['siteLogo'] = $siteLogo;
            $content['pages'] = $pages;
            $content['categories'] = $categories;
            $content['analytics'] = $analytics;
            $content['siteSettings'] = $siteSettings;
            $content['facebookLink'] = $facebookLink;
            $content['twitterLink'] = $twitterLink;
            $content['instagramLink'] = $instagramLink;
            $content['youtubeLink'] = $youtubeLink;
            $content['currencySymbol'] = $currencySymbol;
            $content['couponsEnabled'] = $couponsEnabled;
            $content['sidebarSettings'] = $sidebarSettings;

            



        $this->commonContent = $content;

        // dd($this->commonContent);
        View::share('commonContent', $this->commonContent);
        
    }
}
