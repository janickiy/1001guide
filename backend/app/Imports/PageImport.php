<?php

namespace App\Imports;

use App\Country;
use App\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PageImport implements ToModel, WithHeadingRow
{
	protected const TEMPLATE_FIELDS = [
		"title", "announce", "title_bottom", "content",
		"meta_description"
	];


	public function __construct(string $lang, $model)
	{
		$this->lang = $lang;
		$this->model = $model;
	}


    /**
     * Import Model
     *
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
    	// get current Model
	    $searchCondition = $this->model === Location::class ?
		    ['api_id' => $row['api_id']]:
		    ['country_code' => $row['country_code']];
	    $page = $this->model::where(
		    array_merge($searchCondition, ['lang' => $this->lang])
	    )->first();

	    if ( !$page )
	        return null;

	    // update name
	    foreach (["name", "name_in_case", "name_of_case"] as $nameField) {
	    	if ( empty($row[$nameField]) ) continue;
		    $this->updateName($page, $row[$nameField], $nameField);
	    }

		// update other fields
	    $this->updateFields($page, $row);
    }


	/**
	 * Update Template Fields values
	 *
	 * @param $page
	 * @param $row
	 */
    private function updateFields ($page, $row) {
    	// get fields already was changed
    	$changedFields = $page->changed_fields ?
		    explode(',', $page->changed_fields):
		    [];

    	// "updated" flag
    	$updated = false;

    	// change each field if necessary
    	foreach (self::TEMPLATE_FIELDS as $fieldName) {
    		$oldValue = $page->$fieldName;
		    $xlsFieldName = $this->getXlsFieldName($fieldName);
		    $newValue = $row[$xlsFieldName];
    		// skip "#" fields
    		if ( $newValue === "#" ) continue;
    		// skip identical
		    if ( $oldValue === $newValue ) continue;
		    // save differences
		    $page->$fieldName = $newValue;
		    // mark field as changed
    		if ( !in_array($fieldName, $changedFields) )
    			$changedFields[] = $fieldName;
    		// mark Page as updated to save later
    		$updated = true;
	    }

    	// save if updated
	    if ( !$updated ) return;
	    $page->changed_fields = implode(',', $changedFields);
	    $page->save();
    }


	/**
	 * Get name of Row in the XLS file depending on DB Field name
	 *
	 * @param $fieldName
	 *
	 * @return string
	 */
    private function getXlsFieldName ($fieldName) {
    	if ( $fieldName === 'title' ) return 'h1';
	    if ( $fieldName === 'title_bottom' ) return 'h2';
	    return $fieldName;
    }


	/**
	 * Update Page name
	 *
	 * @param $page
	 * @param $newName
	 *
	 * @return bool
	 */
    private function updateName ($page, $newName, $nameField="name") {
		if ( $page->$nameField === $newName )
			return false;
		$page->$nameField = $newName;
		$page->save();
		return true;
    }
}
