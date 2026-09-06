<?php

require_once __DIR__ . '/../config/query.php';

class SnapshotsService
{



	public function snapshots(array $params)
	{
		
		$CAP_MED_REHAB = $this->procesarDataSimple($params, "EXEC dbo.F_SS_CAP_MED_REHAB");
		$NINOS = $this->procesarDataSimple($params, "EXEC dbo.F_SS_NINOS");
		$MULTIPLES = $this->procesarDataMultiple($params, "EXEC dbo.F_SS_MULTIPLES");
		$GRUPALES = $this->procesarDataMultiple($params, "EXEC dbo.F_SS_GRUPALES");
		$AYUDATECN = $this->procesarDataMultiple($params, "EXEC dbo.F_SS_AYUDATECN");
		$REHABFISIC = $this->procesarDataMultiple($params, "EXEC dbo.F_SS_REHABFISIC");
		$REHABSENSORIAL = $this->procesarDataMultiple($params, "EXEC dbo.F_SS_REHABSENSORIAL");
		$REHABMENTAL = $this->procesarDataMultiple($params, "EXEC dbo.F_SS_REHABMENTAL");
		$CERT_EESS = $this->procesarDataMultiple($params, "EXEC dbo.F_SS_CERT_EESS");
		$COMUNID_AGENTES = $this->procesarDataSimple($params, "EXEC dbo.F_SS_COMUNID_AGENTES");
		$COMUNID_FAMILIAR = $this->procesarDataMultiple($params, "EXEC dbo.F_SS_COMUNID_FAMILIAR");
		$ACTORES = $this->procesarDataSimple($params, "EXEC dbo.F_SS_ACTORES");

		echo json_encode([
			"success" => true,
			"filtros" => $params,
			"data" => [
				...$CAP_MED_REHAB,
				...$NINOS,
				...$MULTIPLES,
				...$GRUPALES,
				...$AYUDATECN,
				...$REHABFISIC,
				...$REHABSENSORIAL,
				...$REHABMENTAL,
				...$CERT_EESS,
				...$COMUNID_AGENTES,
				...$COMUNID_FAMILIAR,
				...$ACTORES
			]
		]);
	}



	function procesarDataSimple(array $params, string $nombreSP){
		$resto = $this->procesarData($params, $nombreSP);
		return [
			$nombreSP => $resto
		];
	}


	function procesarDataMultiple(array $params, string $nombreSP){
		$resto = $this->procesarData($params, $nombreSP);

		$grupo1 = [];
		$grupo2 = [];
		$grupo3 = [];
		$grupo4 = [];
		$grupo5 = [];

		$grupos = [&$grupo1, &$grupo2, &$grupo3, &$grupo4, &$grupo5];

		$i = 0;
		foreach ($resto as $key => $value) {
			$indiceGrupo = $i % 5;
			$grupos[$indiceGrupo][$key] = $value;
			$i++;
		}

		return [
			"$nombreSP - 0D_11A"  => $grupo1,
			"$nombreSP - 12A_17A" => $grupo2,
			"$nombreSP - 18A_29A" => $grupo3,
			"$nombreSP - 30A_59A" => $grupo4,
			"$nombreSP - 60A_mas" => $grupo5,
		];
	}



	function procesarData(array $params, string $nombreSP) { 
		$sql = "
			$nombreSP
				@Anio        = ?,
				@Meses       = ?,
				@Provincia   = ?,
				@Distrito    = ?,
				@MicroRed    = ?,
				@Nombre_EESS = ?,
				@Red         = ?,
				@Desc_UE     = ?
		";

		$values = [
			$params['Anio'] ?? null,
			$params['Mes'] ?? null,
			$params['Provincia'] ?? null,
			$params['Distrito'] ?? null,
			$params['MicroRed'] ?? null,
			$params['Nombre_EESS'] ?? null,
			$params['Red'] ?? null,
			$params['Desc_UE'] ?? null,
		];

		$query = new Query();
		$data = $query->run($sql, $values);

		// Filtra solo las columnas NDQ
		$ndqColumns = array_filter($query->columns, function ($column) {
			return strpos($column, 'NDQ') === 0;
		});

		// Ordenar
		natsort($ndqColumns);

		// Inicializa en 0 respetando el orden natural
		$totals = [];
		foreach ($ndqColumns as $column) {
			$totals[$column] = 0;
		}

		// Suma los valores reales si hay registros
		foreach ($data as $row) {
			foreach ($row as $column => $value) {
				if (strpos($column, 'NDQ') === 0) {
					$totals[$column] += (int) $value;
				}
			}
		}

    	return $totals;

	}





}

