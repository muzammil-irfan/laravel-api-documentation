<?php 
    namespace App\Models;

    class Listing {
        public static function all() {
            return [
                [
                    "id"=> 1,
                ],
                [
                    "id"=> 2,
                ],
                [
                    "id"=> 3,
                ],
            ];
        }

        public static function find($id) {
            $listings = self ::all();
            foreach($listings as $listing) {
                if($listing["id"] == $id){
                    return $listing;
                }
            }
        }
    }