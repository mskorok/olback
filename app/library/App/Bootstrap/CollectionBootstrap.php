<?php

namespace App\Bootstrap;

use App\BootstrapInterface;
use App\Collections\ExportCollection;
// use App\Collections\UserCollection;
use App\Resources\UserResource;
use App\Resources\AlbumResource;
use App\Resources\PhotoResource;
use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconRest\Api;

class CollectionBootstrap implements BootstrapInterface
{
    public function run(Api $api, DiInterface $di, Config $config)
    {
        $api
            ->collection(new ExportCollection('/export'))
            // ->collection(new UserCollection('/user'))
            ->resource(new UserResource('/users'))
            ->resource(new AlbumResource('/albums'))
            ->resource(new PhotoResource('/photos'));
    }
}
// namespace App\Bootstrap;
//
// use App\BootstrapInterface;
// use App\Collections\ExportCollection;
// use App\Collections\DictionaryCollection;
// use App\Collections\UploadCollection;
// use App\Collections\BiimportCollection;
// use App\Collections\BigraphsCollection;
// use App\Collections\DashboardCollection;
// use App\Collections\PassbookCollection;
// use App\Collections\MessengerSettingsCollection;
// use App\Collections\MessengerMessageTypeCollection;
// use App\Collections\MessengerCalendarEventsCollection;
// use App\Collections\MessengerRecipientlistCollection;
// use App\Collections\MessengerEventsCollection;
// use App\Collections\MessengerMessageCollection;
// use App\Collections\MessengerMessageRuleCollection;
// use App\Collections\MessengerEmailBuilderCollection;
// use App\Collections\SystemLanguagesCollection;
// use App\Collections\FieldManagerDictionaryCollection;
// use App\Collections\UtilsCollection;
// use App\Collections\HelperCollection;
// use App\Collections\BudgetingCollection;
// use App\Resources\NightRateScenarioResource;
// use App\Resources\ForecastingScenariosResource;
// use App\Resources\RateConversionAnnualResource;
// use App\Resources\RateConversionPercentagesResource;
// use App\Resources\NightRateResource;
// use App\Resources\AllotmentResource;
// use App\Resources\RoomTypeResource;
// use App\Resources\HotelResource;
// use App\Resources\PricingSeasonResource;
// use App\Resources\SeasonCategoryResource;
// use App\Resources\UserResource;
// use App\Resources\AlbumResource;
// use App\Resources\PhotoResource;
// use App\Resources\ContactDetailsResource;
// use App\Resources\TranslationStaticResource;
// use App\Resources\PkpassTemplateResource;
// use App\Resources\FacilityResource;
// use App\Resources\ReservationResource;
// use App\Resources\BiGraphSaveResource;
// use App\Resources\SeasonResource;
// use App\Resources\TagResource;
// use Phalcon\Acl;
// use Phalcon\Config;
// use Phalcon\DiInterface;
// use PhalconRest\Api;
//
// class CollectionBootstrap implements BootstrapInterface {
//
//     public function run(Api $api, DiInterface $di, Config $config) {
//         $api
//                 ->collection(new ExportCollection('/export'))
//                 ->collection(new DictionaryCollection('/dictionary'))
//                 ->collection(new UploadCollection('/upload'))
//                 ->collection(new BiimportCollection('/bi'))
//                 ->collection(new BigraphsCollection('/bigraphs'))
//                 ->collection(new DashboardCollection('/dashboard'))
//                 ->collection(new PassbookCollection('/pk'))
//                 ->collection(new UtilsCollection('/utils'))
//                 ->collection(new HelperCollection('/helper'))
//                 ->collection(new MessengerSettingsCollection('/messenger/settings'))
//                 ->collection(new MessengerCalendarEventsCollection('/messenger/calendarevents'))
//                 ->collection(new MessengerMessageTypeCollection('/messenger/messagetype'))
//                 ->collection(new MessengerRecipientlistCollection('/messenger/recipientlist'))
//                 ->collection(new MessengerEventsCollection('/messenger/event'))
//                 ->collection(new MessengerMessageCollection('/messenger/message'))
//                 ->collection(new MessengerEmailBuilderCollection('/messenger'))
//                 ->collection(new MessengerMessageRuleCollection('/messenger/n'))
//                 ->collection(new SystemLanguagesCollection('/dictionary/system'))
//                 ->collection(new BudgetingCollection('/budgeting'))
//                 ->collection(new FieldManagerDictionaryCollection('/dictionary/fieldmanager'))
//                 ->resource(new NightRateScenarioResource('/nightRateScenarios'))
//                 ->resource(new ForecastingScenariosResource('/forecastingScenarios'))
//                 ->resource(new RateConversionAnnualResource('/rateConversionAnnuals'))
//                 ->resource(new RateConversionPercentagesResource('/rateConversionPercentages'))
//                 ->resource(new NightRateResource('/nightRates'))
//                 ->resource(new AllotmentResource('/allotments'))
//                 ->resource(new RoomTypeResource('/roomTypes'))
//                 ->resource(new HotelResource('/hotels'))
//                 ->resource(new PricingSeasonResource('/pricingSeasons'))
//                 ->resource(new SeasonCategoryResource('/seasonCategories'))
//                 ->resource(new UserResource('/users'))
//                 ->resource(new ContactDetailsResource('/contacts'))
//                 ->resource(new TranslationStaticResource('/uitranslations'))
//                 ->resource(new PkpassTemplateResource('/pktemplates'))
//                 ->resource(new FacilityResource('/facilities'))
//                 ->resource(new ReservationResource('/reservations'))
//                 ->resource(new BiGraphSaveResource('/savedgraphs'))
//                 ->resource(new SeasonResource('/seasons'))
//                 ->resource(new TagResource('/tags'))
//                 ->resource(new AlbumResource('/albums'))
//                 ->resource(new PhotoResource('/photos'));
//     }
//
// }
