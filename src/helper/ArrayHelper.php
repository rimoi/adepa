<?php

namespace App\helper;

use InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ArrayHelper
{
    /**
     * Retrieve the value of a field from a collection of objects :
     *
     * Input :
     *
     *  $languages =
     *  [
     *     0   => language {
     *              - id => 12451,
     *              - code => 'en',
     *              - name => 'Anglais',
     *           }
     *     //....
     *  ]
     *
     *  $languages = $this->columnize($languages, 'code');
     *
     * Output:
     *
     *  $languages  = ['0' => 'en'
     *                   //...
     *                ]
     *
     */
    public static function columnize(array $items, string $key = null, bool $forceIntValue = false): array
    {
        if (!is_countable($items)) {
            throw new \Exception('The "' . __METHOD__ . '" method does not apply to a variable whose type is not countable');
        }

        if (!\count($items)) {
            return $items;
        }

        $firstValue = self::firstValue($items);

        // array = [['id' => ...], ['id' => ...], ...]
        if (null === $key && \is_array($firstValue)) {
            $key = array_key_first($firstValue);
        }

        if (null === $key) {
            throw new InvalidArgumentException('Aucune clé ' . $key . ' spécifiée.');
        }
        $key = (string) $key;

        if (\is_array($firstValue) && false === strpos($key, '[')) {
            $key = '[' . $key . ']';
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        $column = [];
        foreach ($items as $element) {
            try {
                $value = $accessor->getValue($element, $key);
            } catch (\Exception $e) {
                $value = 'undefined';
            }
            $column[] = $forceIntValue ? (int) $value : $value;
        }

        return $column;
    }

    /**
     * Get the first value of array
     *
     * @return mixed|null
     */
    public static function firstValue(array $items)
    {
        if (!$items) {
            return null;
        }

        return $items[array_key_first($items)];
    }

    /**
     * Crée une tableau associative depuis un tableau d'objets
     *
     * Input :
     *
     *  $languages =
     *  [
     *     0   => language {
     *              - id => 12451,
     *              - code => 'en',
     *              - name => 'Anglais',
     *           }
     *     //....
     *  ]
     *
     *  $languages = $this->createAssociativeArray($languages, 'code', 'name');
     *
     * Output:
     *
     *  $languages  = ['en' => 'Anglais'
     *                      //...
     *                ]
     *
     */
    public static function createAssociativeArray(array $items, string $key, ?string $value = null, bool $multiple = false): array
    {
        if (!\is_array($items)) {
            throw new \Exception('The "' . __METHOD__ . '" method does not apply to a variable whose type is not in array');
        }
        if (!$items) {
            return [];
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        $associative = [];
        foreach ($items as $item) {
            try {
                $itemKey = $accessor->getValue($item, $key);
            } catch (\Exception $e) {
                continue;
            }
            if ($value) {
                $itemValue = $accessor->getValue($item, $value);
            } else {
                $itemValue = $item;
            }

            if ($multiple && !isset($associative[$itemKey])) {
                $associative[$itemKey] = [];
            }

            if ($multiple) {
                $associative[$itemKey][] = $itemValue;
            } else {
                $associative[$itemKey] = $itemValue;
            }
        }

        return $associative;
    }

    /**
     * Regrouper des objets par un champs commun
     *
     * Input :
     *
     *  $languages =
     *  [
     *     0   => language {
     *              - id => 12451,
     *              - code => 'en',
     *              - name => 'Anglais',
     *           }
     *     1   => language {
     *              - id => 12452,
     *              - code => 'en',
     *              - name => 'Anglais (Américain)',
     *           }
     *     //....
     *  ]
     *
     *  $languages = $this->groupBy($languages, 'code');
     *
     * Output:
     *
     *  $languages  = [
     *      'en' => [
     *             0   => language {
     *                - id => 12451,
     *                - code => 'en',
     *                - name => 'Anglais',
     *               }
     *             1   => language {
     *                 - id => 12452,
     *                 - code => 'en',
     *                 - name => 'Anglais (Américain)',
     *               }
     *       ]
     *  //...
     *  ]
     *
     *
     * @param array $items le tableau d'objets
     * @param string $key  la champs ou vous voulez faire le groupBy
     * @param bool $unique Si vous voulez que le tableau puisse contenir des doublons
     */
    public static function groupBy(array $items, string $key, bool $unique = false): array
    {
        if (!is_countable($items)) {
            throw new \Exception('The "' . __METHOD__ . '" method does not apply to a variable whose type is not countable');
        }

        if (!$items) {
            return $items;
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        $groupedItems = [];
        foreach ($items as $element) {
            try {
                $value = $accessor->getValue($element, $key);
            } catch (\Exception $e) {
                $value = 'undefined';
            }

            if ($unique) {
                $groupedItems[$value] = $element;
            } else {
                $groupedItems[$value][] = $element;
            }
        }

        return $groupedItems;
    }
}
