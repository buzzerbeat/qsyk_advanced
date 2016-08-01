<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/5/26
 * Time: 10:47
 */

namespace qsyk\commands;


use qsyk\models\RandomCache;
use qsyk\models\RandomQueue;
use qsyk\models\Resource;
use yii\console\Controller;
use yii\db\Query;

class DataController extends Controller
{

    const TYPE_INDEX_150 = 104;
    const TYPE_INDEX_1700 = 106;
    const TYPE_INDEX_150_300 = 105;
    const TYPE_INDEX_300 = 101;
    const TYPE_INDEX_450 = 102;
    const TYPE_INDEX_7000 = 103;

    const CATEGORY_INDEX = 101;
    public function actionCacheIndex() {

        $time_start = microtime(true);
        $_row300 = RandomQueue::find()
            ->select('resource_id')
            ->where(['type'=>self::TYPE_INDEX_300])
            ->column();

        $_row450 = RandomQueue::find()
            ->select('resource_id')
            ->where(['type'=>self::TYPE_INDEX_450])
            ->column();

        $_row7000 = RandomQueue::find()
            ->select('resource_id')
            ->where(['type'=>self::TYPE_INDEX_7000])
            ->column();
        shuffle($_row300);
        shuffle($_row450);
        shuffle($_row7000);
        $_newGroup300 = array_chunk($_row300, 20);
        $_newGroup450 =array_chunk($_row450, 10);
        $_newGroup7000 = array_chunk($_row7000, 10);



        for($i = 0;$i < 15;$i++) {
            echo "Group $i:\n";
            $_newGroup7000TotalCount = count($_newGroup7000);
            $rest = 40;
            $usedCount = 0;
            if (isset($_newGroup300[$i])) {
                $this->appendCaches($_newGroup300[$i], self::CATEGORY_INDEX, $i * 40 + $usedCount);
                $rest -= count($_newGroup300[$i]);
                $usedCount += count($_newGroup300[$i]);
            }

            echo "\trest $rest resource\n";

            if (isset($_newGroup450[$i])) {
                $this->appendCaches($_newGroup450[$i], self::CATEGORY_INDEX, $i * 40 + $usedCount);
                $rest -= count($_newGroup450[$i]);
                $usedCount += count($_newGroup450[$i]);
            }

            echo "\trest $rest resource\n";


            if (isset($_newGroup7000[$i])) {
                $this->appendCaches($_newGroup7000[$i], self::CATEGORY_INDEX, $i * 40 + $usedCount);
                $rest -= count($_newGroup7000[$i]);
                $usedCount += count($_newGroup7000[$i]);
            }


            echo "\trest $rest resource\n";

            for($t = 0;$t < 4;$t++) {
                if ($rest <= 0) {
                    break;
                }
                if ($_newGroup7000TotalCount - $t > 0 && isset($_newGroup7000[$_newGroup7000TotalCount - $t - 1])) {
                    $this->appendCaches($_newGroup7000[$_newGroup7000TotalCount - $t - 1], self::CATEGORY_INDEX, $i * 40 + $usedCount, $rest);
                    $rest -= count($_newGroup7000[$_newGroup7000TotalCount - $t - 1]);
                    $usedCount += count($_newGroup7000[$_newGroup7000TotalCount - $t - 1]);
                    unset($_newGroup7000[$_newGroup7000TotalCount - $t - 1]);
                }
                echo "\trest $rest resource\n";
            }
        }

        for($i = 0;$i < 15;$i++) {
            $rest = 40;
            echo "Group $i:\n";
            $_newGroup7000TotalCount = count($_newGroup7000);
            $usedCount = 0;
            if (isset($_newGroup450[15 + (2 * $i)])) {
                $this->appendCaches($_newGroup450[15 + (2 * $i)], self::CATEGORY_INDEX, ($i + 15) * 40 + $usedCount);
                $rest -= count($_newGroup450[15 + (2 * $i)]);
                $usedCount += count($_newGroup450[15 + (2 * $i)]);
            }

            echo "\trest $rest resource\n";

            if (isset($_newGroup450[15 + (2 * $i) + 1])) {
                $this->appendCaches($_newGroup450[15 + (2 * $i) + 1], self::CATEGORY_INDEX, ($i + 15) * 40 + $usedCount);
                $rest -= count($_newGroup450[15 + (2 * $i) + 1]);
                $usedCount += count($_newGroup450[15 + (2 * $i) + 1]);
            }
            echo "\trest $rest resource\n";

            if (isset($_newGroup7000[15 + (2 * $i)])) {
                $this->appendCaches($_newGroup7000[15 + (2 * $i)], self::CATEGORY_INDEX, ($i + 15) * 40 + $usedCount);
                $rest -= count($_newGroup7000[15 + (2 * $i)]);
                $usedCount += count($_newGroup7000[15 + (2 * $i)]);
            }

            echo "\trest $rest resource\n";

            if (isset($_newGroup7000[15 + (2 * $i) + 1])) {
                $this->appendCaches($_newGroup7000[15 + (2 * $i) + 1], self::CATEGORY_INDEX, ($i + 15) * 40 + $usedCount);
                $rest -= count($_newGroup7000[15 + (2 * $i) + 1]);
                $usedCount += count($_newGroup7000[15 + (2 * $i) + 1]);
            }

            echo "\trest $rest resource\n";

            for($t = 0;$t < 4;$t++) {
                if ($rest <= 0) {
                    break;
                }
                if ($_newGroup7000TotalCount - $t > 0 && isset($_newGroup7000[$_newGroup7000TotalCount - $t - 1])) {
                    $this->appendCaches($_newGroup7000[$_newGroup7000TotalCount - $t - 1], self::CATEGORY_INDEX, ($i + 15) * 40 + $usedCount, $rest);
                    $rest -= count($_newGroup7000[$_newGroup7000TotalCount - $t - 1]);
                    $usedCount += count($_newGroup7000[$_newGroup7000TotalCount - $t - 1]);
                    unset($_newGroup7000[$_newGroup7000TotalCount - $t - 1]);
                }
                echo "\trest $rest resource\n";
            }
        }
        $i = 0;
        while(isset($_newGroup7000[45 + $i])) {
            $baseIndex = 30 * 40 + 10 * $i;
            echo "\tbase {$baseIndex} resource\n";
            $this->appendCaches($_newGroup7000[45 + $i], self::CATEGORY_INDEX, $baseIndex);
            $i ++;
        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "cache  in $time seconds\n";
    }

    public function actionCacheCategory($type) {
        $category = self::CATEGORY_INDEX + $type * 100;
        $time_start = microtime(true);
        $_row150 = RandomQueue::find()
            ->select('resource_id')
            ->where(['type'=>self::TYPE_INDEX_150 + $type * 100])
            ->column();

        $_row150_300 = RandomQueue::find()
            ->select('resource_id')
            ->where(['type'=>self::TYPE_INDEX_150_300 + $type * 100])
            ->column();

        $_row1700 = RandomQueue::find()
            ->select('resource_id')
            ->where(['type'=>self::TYPE_INDEX_1700 + $type * 100])
            ->column();
        shuffle($_row150);
        shuffle($_row150_300);
        shuffle($_row1700);
        $_newGroup150 = array_chunk($_row150, 15);
        $_newGroup150_300 = array_chunk($_row150_300, 5);
        $_newGroup1700 = array_chunk($_row1700, 10);

        for($i = 0;$i < 10;$i++) {
            echo "Group $i:\n";
            $_newGroup1700TotalCount = count($_newGroup1700);
            $rest = 40;
            $usedCount = 0;
            if (isset($_newGroup150[$i])) {
                $this->appendCaches($_newGroup150[$i], $category, $i * 40 + $usedCount);
                $rest -= count($_newGroup150[$i]);
                $usedCount += count($_newGroup150[$i]);
            }
            echo "\trest $rest resource\n";

            if (isset($_newGroup150_300[$i])) {
                $this->appendCaches($_newGroup150_300[$i], $category, $i * 40 + $usedCount);
                $rest -= count($_newGroup150_300[$i]);
                $usedCount += count($_newGroup150_300[$i]);
            }

            echo "\trest $rest resource\n";

            if (isset($_newGroup1700[2 * $i])) {
                $this->appendCaches($_newGroup1700[2 * $i], $category, $i * 40 + $usedCount);
                $rest -= count($_newGroup1700[2 * $i]);
                $usedCount += count($_newGroup1700[2 * $i]);
            }

            echo "\trest $rest resource\n";


            if (isset($_newGroup1700[2 * $i + 1])) {
                $this->appendCaches($_newGroup1700[2 * $i + 1], $category, $i * 40 + $usedCount);
                $rest -= count($_newGroup1700[2 * $i + 1]);
                $usedCount += count($_newGroup1700[2 * $i + 1]);
            }

            echo "\trest $rest resource\n";


            for($t = 0;$t < 4;$t++) {
                if ($rest <= 0) {
                    break;
                }
                if ($_newGroup1700TotalCount - $t > 0 && isset($_newGroup1700[$_newGroup1700TotalCount - $t - 1])) {
                    $this->appendCaches($_newGroup1700[$_newGroup1700TotalCount - $t - 1], $category, $i * 40 + $usedCount, $rest);
                    $rest -= count($_newGroup1700[$_newGroup1700TotalCount - $t - 1]);
                    $usedCount += count($_newGroup1700[$_newGroup1700TotalCount - $t - 1]);
                    unset($_newGroup1700[$_newGroup1700TotalCount - $t - 1]);
                }
                echo "\trest $rest resource\n";
            }


        }
        $offset = 10;
        for($i = 0;$i < $offset;$i++) {
            $rest = 40;
            echo "Group $i:\n";
            $_newGroup1700TotalCount = count($_newGroup1700);
            $usedCount = 0;
            if (isset($_newGroup150_300[$offset + (2 * $i)])) {
                $this->appendCaches($_newGroup150_300[$offset + (2 * $i)], $category, ($i + $offset) * 40 + $usedCount);
                $rest -= count($_newGroup150_300[$offset + (2 * $i)]);
                $usedCount += count($_newGroup150_300[$offset + (2 * $i)]);
            }

            echo "\trest $rest resource\n";

            if (isset($_newGroup150_300[$offset + (2 * $i) + 1])) {
                $this->appendCaches($_newGroup150_300[$offset + (2 * $i) + 1], $category, ($i + $offset) * 40 + $usedCount);
                $rest -= count($_newGroup150_300[$offset + (2 * $i) + 1]);
                $usedCount += count($_newGroup150_300[$offset + (2 * $i) + 1]);
            }
            echo "\trest $rest resource\n";

            if (isset($_newGroup1700[$offset * 2 + (3 * $i)])) {
                $this->appendCaches($_newGroup1700[$offset * 2 + (3 * $i)], $category, ($i + $offset) * 40 + $usedCount);
                $rest -= count($_newGroup1700[$offset * 2 + (3 * $i)]);
                $usedCount += count($_newGroup1700[$offset * 2 + (3 * $i)]);
            }

            echo "\trest $rest resource\n";

            if (isset($_newGroup1700[$offset * 2 + (3 * $i) + 1])) {
                $this->appendCaches($_newGroup1700[$offset * 2 + (3 * $i) + 1], $category, ($i + $offset) * 40 + $usedCount);
                $rest -= count($_newGroup1700[$offset * 2 + (3 * $i) + 1]);
                $usedCount += count($_newGroup1700[$offset * 2 + (3 * $i) + 1]);
            }

            echo "\trest $rest resource\n";

            if (isset($_newGroup1700[$offset * 2 + (3 * $i) + 2])) {
                $this->appendCaches($_newGroup1700[$offset * 2 + (3 * $i) + 2], $category, ($i + $offset) * 40 + $usedCount);
                $rest -= count($_newGroup1700[$offset * 2 + (3 * $i) + 2]);
                $usedCount += count($_newGroup1700[$offset * 2 + (3 * $i) + 2]);
            }

            echo "\trest $rest resource\n";

            for($t = 0;$t < 4;$t++) {
                if ($rest <= 0) {
                    break;
                }
                if ($_newGroup1700TotalCount - $t > 0 && isset($_newGroup1700[$_newGroup1700TotalCount - $t - 1])) {
                    $this->appendCaches($_newGroup1700[$_newGroup1700TotalCount - $t - 1], $category, ($i + $offset) * 40 + $usedCount, $rest);
                    $rest -= count($_newGroup1700[$_newGroup1700TotalCount - $t - 1]);
                    $usedCount += count($_newGroup1700[$_newGroup1700TotalCount - $t - 1]);
                    unset($_newGroup1700[$_newGroup1700TotalCount - $t - 1]);
                }
                echo "\trest $rest resource\n";
            }
        }
        $i = 0;
        while(isset($_newGroup1700[50 + $i])) {
            $baseIndex = 20 * 40 + 10 * $i;
            echo "\tbase {$baseIndex} resource\n";
            $this->appendCaches($_newGroup1700[50 + $i], $category, $baseIndex);
            $i ++;
        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "cache  in $time seconds\n";
    }


    public function actionPrepare($allUpdate = false) {
        $startTime = mktime(date('H') - 24, date('i'), date('s'), date('m'), date('d'), date('y'));
        $endTime = mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('y'));

        $time_start = microtime(true);
        $now = time();
        $_row300 = (new Query())
            ->select(['id'])
            ->from('resource')
            ->where(['status' => Resource::STATUS_ACTIVE])
            ->andWhere(['between', 'pub_time', $startTime, $endTime])
            ->andWhere('valid_time = 0 or valid_time > :time', [':time' => $now])
            ->orderBy(['pub_time' => SORT_DESC])
            ->limit(300)
            ->column();

        $this->saveToDb($_row300, self::TYPE_INDEX_300);

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "300 Rows Query in $time seconds\n";


        $time_start = microtime(true);



        if ($allUpdate) {
            $startTime = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 4, date('y'));
            $endTime = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 1, date('y'));
            $_row450 = (new Query())
                ->select(['id'])
                ->from('resource')
                ->where(['status' => Resource::STATUS_ACTIVE])
                ->andWhere(['between', 'pub_time', $startTime, $endTime])
                ->andWhere('valid_time = 0 or valid_time > :time', [':time' => $now])
                ->orderBy('RAND()')
                ->limit(450)
                ->column();
            $this->saveToDb($_row450, self::TYPE_INDEX_450);
        }


        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "450 Rows Query in $time seconds\n";

        $time_start = microtime(true);

        if ($allUpdate) {
            $startTime = mktime(date('H'), date('i'), date('s'), date('m') - 1, date('d'), date('y'));
            $endTime = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 4, date('y'));
            $_row7k = (new Query())
                ->select(['id'])
                ->from('resource')
                ->where(['status' => Resource::STATUS_ACTIVE])
                ->andWhere(['between', 'pub_time', $startTime, $endTime])
                ->andWhere('valid_time = 0 or valid_time > :time', [':time' => $now])
                ->orderBy('RAND()')
                ->limit(7000)
                ->column();
            $this->saveToDb($_row7k, self::TYPE_INDEX_7000);
        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "7000 Rows Query in $time seconds\n";


    }


    public function actionPrepareCategory($type ,$allUpdate = false) {


        $time_start = microtime(true);
        $now = time();
        $startTime = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 2, date('y'));
        $endTime = mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('y'));
        if ($type == Resource::TYPE_DYNAMIC) {
            $query = (new Query())
                ->select(['resource.id'])
                ->from('resource')
                ->leftJoin('resource_relation', '`resource_relation`.`resource_id` = `resource`.`id`')
                ->where([
                    'status' => Resource::STATUS_ACTIVE,
                    'type' =>  Resource::TYPE_IMAGE,
                    'resource_relation.dynamic'=>1
                ]);
        } else if ($type == Resource::TYPE_IMAGE) {
            $query = (new Query())
                ->select(['resource.id'])
                ->from('resource')
                ->leftJoin('resource_relation', '`resource_relation`.`resource_id` = `resource`.`id`')
                ->where([
                    'status' => Resource::STATUS_ACTIVE,
                    'type' => Resource::TYPE_IMAGE,
                    'resource_relation.dynamic'=>0
                ]);

        } else if ($type == Resource::TYPE_MULTI_IMG) {
            $query = (new Query())
                ->select(['resource.id'])
                ->from('resource')
                ->leftJoin('resource_relation', '`resource_relation`.`resource_id` = `resource`.`id`')
                ->where([
                    'status' => Resource::STATUS_ACTIVE,
                    'type' => Resource::TYPE_MULTI_IMG,
                ]);
        } else {
            $query = (new Query())
                ->select(['id'])
                ->from('resource')
                ->where([
                    'status' => Resource::STATUS_ACTIVE,
                    'type' => $type
                ]);
        }

        $_150query = clone $query;
        $_row150 = $_150query
            ->andWhere(['between', 'pub_time', $startTime, $endTime])
            ->andWhere('valid_time = 0 or valid_time > :time', [':time' => $now])
            ->orderBy(['pub_time' => SORT_DESC])
            ->limit(150)
            ->column();

        $this->saveToDb($_row150, self::TYPE_INDEX_150 + $type * 100);


        if ($allUpdate) {
            $startTime = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 7, date('y'));
            $endTime = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 2, date('y'));
            $_150_300query = clone $query;
            $_row150_300 = $_150_300query
                ->andWhere(['between', 'pub_time', $startTime, $endTime])
                ->andWhere('valid_time = 0 or valid_time > :time', [':time' => $now])
                ->orderBy('RAND()')
                ->limit(150)
                ->column();
            $this->saveToDb($_row150_300, self::TYPE_INDEX_150_300 + $type * 100);
        }

        if ($allUpdate) {
            $startTime = mktime(date('H'), date('i'), date('s'), date('m') - 1, date('d'), date('y'));
            $endTime = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 7, date('y'));
            $_1k7query = clone $query;
            $_row1k7 = $_1k7query
                ->andWhere(['between', 'pub_time', $startTime, $endTime])
                ->andWhere('valid_time = 0 or valid_time > :time', [':time' => $now])
                ->orderBy('RAND()')
                ->limit(1700)
                ->column();
            echo "row1700 ". count($_row1k7) ."filtered\n";
            $this->saveToDb($_row1k7, self::TYPE_INDEX_1700 + $type * 100);
        }





        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo "2000 Rows Query in $time seconds\n";


    }

    private function saveToDb($rows, $type) {
        foreach ($rows as $idx => $row) {
            $random = RandomQueue::find()->where([
                'type'=>$type,
                'index'=>$idx + 1,
            ])->one();
            if (empty($random)) {
                $random = new RandomQueue();
                $random->type = $type;
                $random->index = $idx + 1;
            }
            $random->resource_id = $row;
            if (!$random->save()) {
                var_dump($random->getErrors());
                exit;
            }

        }
    }


    private function appendCaches($rows, $cat, $baseIdx, $limit = 0) {
        foreach ($rows as $idx => $row) {
            if ($limit > 0 && $idx + 1 > $limit) {
                return;
            }
            $random = RandomCache::find()->where([
                'category'=>$cat,
                'index'=>$baseIdx + $idx + 1,
            ])->one();
            if (empty($random)) {
                $random = new RandomCache();
                $random->category = $cat;
                $random->index = $baseIdx + $idx + 1;
            }
            $random->resource_id = $row;
            $random->updated_at = time();
            if (!$random->save()) {
                var_dump($random->getErrors());
                exit;
            }

        }
    }

}