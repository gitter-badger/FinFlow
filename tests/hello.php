<?php
/**
 * FinFlow 1.0 - Hello file
 * @author Adrian S.
 * @version 1.0
 */

$days = 180;

$datestart = date('Y-m-d');
$dateend  = date('Y-m-d', @strtotime('+' . $days . ' days'));

$SelectSQL = "

SELECT

        `trans_id`,
        `root_id`,
        `recurring`,
        `value`,
        `optype`,
        `currency_id`,
        `sdate`,
        `fdate`,
        `metadata`,

        `c_days` AS `period_days`,

        `instances` AS `period_instances`,

       (`instances` * `value`) AS `period_total`

    FROM (

        SELECT
            *,

            ( `day_count_till_datepay` - ABS(`day_count_till_enable`) + `day_count_topay` ) AS `c_days`,

            (
              CASE
                WHEN `recurring` = 'daily'      THEN ( FLOOR( ( `day_count_till_datepay` - ABS(`day_count_till_enable`) + `day_count_topay` ) / 1 ) )
                WHEN `recurring` = 'monthly' THEN ( FLOOR( ( `day_count_till_datepay` - ABS(`day_count_till_enable`) + `day_count_topay` ) / 30 ) )
                WHEN `recurring` = 'yearly'    THEN ( FLOOR( ( `day_count_till_datepay` - ABS(`day_count_till_enable`) + `day_count_topay` ) / 365 ) )
              END
            )
            AS `instances`

            FROM (

                SELECT

                    `trans_id`,
                    `root_id`,
                    `value`,
                    `optype`,
                    `currency_id`,
                    `sdate`,
                    `fdate`,
                    `metadata`,
                    `recurring`,
                    DATEDIFF( `fdate` , '{$datestart}' ) AS `day_count_till_datepay` ,
                    DATEDIFF( '{$dateend}' , `fdate` ) AS `day_count_topay` ,

                    (
                        CASE
                            WHEN `sdate` <= '{$datestart}' THEN 0
                            WHEN `sdate` >  '{$datestart}' THEN DATEDIFF( `sdate` , '{$datestart}' )
                        END
                    )

                    AS `day_count_till_enable`

                FROM `cash_op_pending`

                WHERE `root_id`=0
                AND `fdate` >= '{$datestart}'
                AND `fdate` <= '{$dateend}'
                AND `active` = 'yes'
        ) AS Q

) AS R";

echo "<pre>$SelectSQL";