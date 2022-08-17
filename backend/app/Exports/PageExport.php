<?php

namespace App\Exports;

use App\Country;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PageExport implements FromCollection, WithHeadings
{
	protected static $MODEL = Country::class;
	protected const FIELDS = [
		"id", "name", "name_in_case", "name_of_case", "changed_fields", "country_code"
	];
	protected const TEMPLATE_FIELDS = [
		"title", "announce", "title_bottom", "content",
		"meta_description"
	];


	public function __construct(string $lang)
	{
		$this->lang = $lang;
	}


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	// get all entries
    	$pages = $this->getModels();

	    // replace templatable values to "#"
    	foreach ($pages as $page) {
		    $changedFields = $page->changed_fields ?
			    explode(",", $page->changed_fields):
		        [];
		    foreach (self::TEMPLATE_FIELDS as $fieldName) {
		    	if ( in_array($fieldName, $changedFields) ) continue;
		    	$page->$fieldName = "#";
		    }
	    }

        return $pages;
    }


	public function headings(): array
	{
		return [
			'ID',
			'Name',
			'Name In Case',
			'Name Of Case',
			'Changed Fields',
			'Country Code',
			'H1',
			'Announce',
			'H2',
			'Content',
			'Meta Description',
			'API ID'
		];
	}


	protected function getModels () {
    	return static::$MODEL::where([
		    'lang' => $this->lang
	    ])->select(array_merge(self::FIELDS, self::TEMPLATE_FIELDS))
	      ->orderBy("name", "asc")
	      ->get();
	}
}
