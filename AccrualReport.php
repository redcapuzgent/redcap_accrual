<?php

namespace uzgent\AccrualReport;

// Declare your module class, which must extend AbstractExternalModule
class AccrualReport extends \ExternalModules\AbstractExternalModule {

    public function getProcessAccrualURL() {
        return $this->getUrl("accrual.php", false, false);
    }



    public function getDAG($groups, string $selectedGroup)
    {
        $group_id = null;
        foreach ($groups as $key => $group) {
            if ($group == $selectedGroup) {
                $group_id = $key;
            }
        }
        return $group_id;
    }

    /**
     * @param string $datefieldname
     * @return array
     */
    public function getDagMapForField($datefieldname)
    {
        $dates = [];
        $datesForDAG = []; //map of [dag] => array of dates for that dag.
        $params = ['exportDataAccessGroups' => true];
        $data = \REDCap::getData($params);
        $dags = [];
        foreach ($data as $recordId => $recordDetails) {
            foreach ($recordDetails as $event => $recordDetailsDeep) {
                $dag = $recordDetailsDeep["redcap_data_access_group"];
                if ($dag == '') {
                    $dag = NODAG_NAME;
                }
                $dags[] = $dag;
                $datesForDAG [$dag] [] = $recordDetailsDeep[$datefieldname];
                $dates[] = $recordDetailsDeep[$datefieldname];
            }
        }

        $datemapPerDag = []; //map of [date][dag] = count.  e.g. [2020-06-05]['dag1'] = 5
        foreach (array_unique($dates) as $uniqueDate) {
            foreach ($datesForDAG as $dag => $dagDates) {
                $datemapPerDag[$uniqueDate][$dag] = $this->getAccrualCountForDag($dagDates, $uniqueDate);
            }
        }
        $uniquedags = array_unique($dags);
        $dagList = "'" . implode($uniquedags, "','") . "'";
        return array($datemapPerDag, $dagList, $uniquedags);
    }

    /**
     * @param $datemapPerDag
     * @return array
     */
    public function printDagMap($datemapPerDag, $dags)
    {
        $dateKeys = array_keys($datemapPerDag);
        sort($dateKeys); //dates must always be sorted in ascending order to display.

        foreach ($dateKeys as $dateKey) {

            echo "['" . $dateKey . "', " . $this->returnDagSortedCumul($datemapPerDag[$dateKey], $dags) . "],";
        }
        return $dateKeys;
    }

    /**
     * Why not just implode the map?
     * Well you just don't know for sure the dags will have the same sorting order!
     *
     * @param $map
     * @param $dags
     * @return string
     */
    function returnDagSortedCumul($map, $dags)
    {
        $imploded = "";
        foreach ($dags as $dag) {
            $imploded .= $map[$dag] .",";
        }
        return $imploded;
    }

    /**
     * @param $dagDates
     * @param $uniqueDate
     * @return int
     */
    public function getAccrualCountForDag($dagDates, $uniqueDate): int
    {
        $nrDatesBeforeUnique = 0;
        foreach ($dagDates as $date) {
            if ($date <= $uniqueDate) {
                $nrDatesBeforeUnique++;
            }
        }
        return $nrDatesBeforeUnique;
    }

}
