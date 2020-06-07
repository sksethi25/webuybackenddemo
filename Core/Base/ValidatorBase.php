<?php

namespace Core\Base;

interface ValidatorBase{
	public function validate(array $ruleList, callable $input_mapper=null, array $external_inputs=[]);
}