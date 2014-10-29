<?php
/**
 * Faker test of available stuff
 */

echo '<div style="font-family: \'Trebuchet MS\', \'Helvetica Neue\', Arial, Helvetica, san-serif; font-size: 1.3em;">';

require_once '../../../thirdparty/Faker-master/src/autoload.php';

$faker = Faker\Factory::create();

echo  '<p><code style="color: #80c"> $faker->name </code> => <em>' . $faker->name . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->url </code> => <em>' . $faker->url . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->address </code> => <em>' . $faker->address . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->userName </code> => <em>' . $faker->userName . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->ipv4 </code> => <em>' . $faker->ipv4 . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->domainWord </code> => <em>' . $faker->domainWord . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->safeEmailDomain </code> => <em>' . $faker->safeEmailDomain . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->companyEmail </code> => <em>' . $faker->companyEmail . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->safeEmail </code> => <em>' . $faker->safeEmail . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->email </code> => <em>' . $faker->email . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->hexColor </code> => <em>' . $faker->hexColor . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->rgbColor </code> => <em>' . $faker->rgbColor . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->colorName </code> => <em>' . $faker->colorName . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->mimeType </code> => <em>' . $faker->mimeType . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->fileExtension </code> => <em>' . $faker->fileExtension . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->sentences </code> => <em>' . $faker->sentences . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->paragraph </code> => <em>' . $faker->paragraph . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->text </code> => <em>' . $faker->text . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->md5 </code> => <em>' . $faker->md5 . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->sha1 </code> => <em>' . $faker->sha1 . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->sha256 </code> => <em>' . $faker->sha256 . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->locale </code> => <em>' . $faker->locale . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->countryCode </code> => <em>' . $faker->countryCode . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->languageCode </code> => <em>' . $faker->languageCode . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->firstName </code> => <em>' . $faker->firstName . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->lastName </code> => <em>' . $faker->lastName . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->iso8601 </code> => <em>' . $faker->iso8601 . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->date </code> => <em>' . $faker->date . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->time </code> => <em>' . $faker->time . '</em></p>';
//echo  '<p><code style="color: #80c"> $faker->dateTimeBetween </code> => <em>' . $faker->dateTimeBetween . '</em></p>';
//echo  '<p><code style="color: #80c"> $faker->dateTimeThisYear </code> => <em>' . $faker->dateTimeThisYear . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->dayOfWeek </code> => <em>' . $faker->dayOfWeek . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->monthName </code> => <em>' . $faker->monthName . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->year </code> => <em>' . $faker->year . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->timezone </code> => <em>' . $faker->timezone . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->randomDigit </code> => <em>' . $faker->randomDigit . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->randomNumber </code> => <em>' . $faker->randomNumber . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->randomNumber(4, 99) </code> => <em>' . $faker->randomNumber(3, 1000) . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->numberBetween(1, 100) </code> => <em>' . $faker->numberBetween(1, 100) . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->randomFloat </code> => <em>' . $faker->randomFloat . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->randomFloat(2, 1000, 2000) </code> => <em>' . $faker->randomFloat(2, 1000, 2000) . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->randomLetter </code> => <em>' . $faker->randomLetter . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->phoneNumber </code> => <em>' . $faker->phoneNumber . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->company </code> => <em>' . $faker->company . '</em></p>';
echo  '<p><code style="color: #80c"> $faker->boolean </code> => <em>' . strval( $faker->boolean(50) ) . '</em></p>';