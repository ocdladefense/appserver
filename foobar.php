<?php

		$put = 365 - $days;

		$d1 = new \DateTime();
		$d2 = new \DateTime();
		$d1->modify('-365 day');
		$d2->modify('-'.$put.' day');

		$r1 = $d1->format('Y-m-d');
		$r2 = $d2->format('Y-m-d');

        print $r2;