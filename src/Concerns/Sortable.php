<?php

namespace Ipsum\Reservation\Concerns;

trait Sortable
{

    protected static function bootOrder()
    {

        self::saving(function(self $objet)
        {
            if (!$objet->exists) {

                // On récupère le dernier order
                $objet->order = self::where('parent_id', $objet->parent_id)->count() + 1;

            }
            // Cas du changement de parent
            elseif ($objet->getOriginal('parent_id') != $objet->parent_id) {

                // On récupère le dernier order
                $objet->order = self::where('parent_id', $objet->parent_id)->count() + 1;

                self::updateOrder($objet->getOriginal('parent_id'), $objet->id);

            }
        });

        self::deleted(function(self $objet)
        {
            self::updateOrder($objet->parent_id);
        });
    }



    static public function updateOrder($parent_id = null, $exclude_id = null)
    {
        $query = self::select(['id', 'order', 'parent_id'])/*->where('id', '!=', )*/;
        if ($parent_id !== null) {
            $query->where('parent_id', $parent_id);
        }
        if ($exclude_id !== null) {
            $query->where('id', '!=', $exclude_id);
        }
        $objets = $query->orderBy('order', 'asc')->orderBy('id', 'asc')->get();

        $datas = [];
        foreach ($objets as $objet) {
            $datas[$objet->parent_id][] = $objet;
        }

        foreach ($datas as $data) {
            $order = 1;
            foreach ($data as $objet) {
                $objet->order = $order;
                $objet->save();
                $order++;
            }
        }
    }

}
