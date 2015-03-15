<?php

class distance {
	public $origin;
	public $destination;
	public $distance;
	public $offset;
	public $entities_per_page;

	public $coordinates_array;

	// uses post codes to populate coordinates_array with lon and lat
	public function populate_coordinates() {
		global $conn;

		$query = "select postcodelatlng.latitude, postcodelatlng.longitude from postcodelatlng where postcodelatlng.postcode = ";

		$origin_query = $query."'".$this->origin."'";
		$origin_result = $conn->query($origin_query);

		while($row = $origin_result->fetch_assoc()) {
			$this->coordinates_array['origin']['latitude'] = $row['latitude'];
			$this->coordinates_array['origin']['longitude'] = $row['longitude'];
		}

		if (!empty($this->destination))	{
			$destination_query = $query."'".$this->destination."'";
			$destination_result = $conn->query($destination_query);

			while($row = $destination_result->fetch_assoc()) {
				$this->coordinates_array['destination']['latitude'] = $row['latitude'];
				$this->coordinates_array['destination']['longitude'] = $row['longitude'];
			}
		}
	}
	
	private function get_search_query() {
		$search_query = "SELECT distinct ice_cream_shops.id,
       (((acos(sin((".$this->coordinates_array['origin']['latitude']."*pi()/180)) * sin((`Latitude`*pi()/180))+cos((".$this->coordinates_array['origin']['latitude']."*pi()/180)) * cos((`Latitude`*pi()/180)) * cos(((".$this->coordinates_array['origin']['longitude']." - `Longitude`)*pi()/180))))*180/pi())*60*1.1515) AS distance
       FROM `postcodelatlng`
       INNER JOIN ice_cream_shops ON ice_cream_shops.post_code = postcodelatlng.postcode";

		$search_query .= " ORDER BY distance ASC limit $this->offset, $this->entities_per_page";

		return $search_query;
	}

	public function build_result() {
		global $conn;

		$search_query = $this->get_search_query();
		$result = $conn->query($search_query);

		for ($count = 0; $row = $result->fetch_assoc(); $count++) {
			print_r($row);
		}
	}
}
?>
