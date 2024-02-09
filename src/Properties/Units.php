<?php 
return [
    'none'=>[
        'unit'=>'',
        'group'=>'none',
        'basic'=>'none',
        'calculate_to'=>null,
        'calculate_from'=>null        
    ],    
// **************************************** Length ************************************
    'meter'=>[
        'unit'=>'m',
        'group'=>'length',
        'basic'=>'meter',
        'calculate_to'=>null,
        'calculate_from'=>null        
    ],
    'centimeter'=>[
        'unit'=>'cm',
        'group'=>'length',
        'basic'=>'meter',
        'calculate_to'=>function($input) { return $input / 100; },
        'calculate_from'=>function($input) { return $input * 100; }
        ],
     'millimeter'=>[
            'unit'=>'mm',
         'group'=>'length',
         'basic'=>'meter',
            'calculate_to'=>function($input) { return $input / 1000; },
            'calculate_from'=>function($input) { return $input * 1000; }
        ],
     'kilometer'=>[
            'unit'=>'km',
         'group'=>'length',
         'basic'=>'meter',
            'calculate_to'=>function($input) { return $input * 1000; },
            'calculate_from'=>function($input) { return $input / 1000; }
        ],
     
// ************************************ weight *****************************************     
     'kilogramm'=>[
            'unit'=>'kg',
         'group'=>'weight',
         'basic'=>'kilogramm',
            'calculate_to'=>null,
            'calculate_from'=>null
        ],
     'gramm'=>[
            'unit'=>'g',
         'group'=>'weight',
         'basic'=>'kilogramm',
            'calculate_to'=>function($input) { return $input / 1000; },
            'calculate_from'=>function($input) { return $input * 1000; }
        ],

// ************************************ Temperature ***************************************        
     'degreecelsius'=>[
         'unit'=>'°C',
         'group'=>'temperature',
         'basic'=>'degreecelsius',
         'calculate_to'=>null,
         'calculate_from'=>null
        ],
     'degreekelvin'=>[
         'unit'=>'K',
         'group'=>'temperature',
         'basic'=>'degreecelsius',
         'calculate_to'=>function($input) { return $input - 273.15; },
         'calculate_from'=>function($input) { return $input + 273.15; }
        ],
      'degreefahrenheit'=>[
          'unit'=>'F',
          'group'=>'temperature',
          'basic'=>'degreecelsius',
          'calculate_to'=>function($input) { return ($input - 32) * 5/9; },
          'calculate_from'=>function($input) { return $input * 1.8 + 32; }
        ],
        
// ********************************** Speed ************************************************        
        'meterpersecond'=>[
            'unit'=>'m/s',
            'group'=>'speed',
            'basic'=>'meterpersecond',
            'calculate_to'=>null,
            'calculate_from'=>null
        ],
        'kilometerperhour'=>[
            'unit'=>'km/h',
            'group'=>'speed',
            'basic'=>'meterpersecond',
            'calculate_to'=>function($input) { return $input / 3.6; },
            'calculate_from'=>function($input) { return $input * 3.6; }
        ],
        
// ********************************* Time ****************************************
        'second'=>[
            'unit'=>'s',
            'group'=>'duration',
            'basic'=>'second',
            'calculate_to'=>null,
            'calculate_from'=>null
        ],
        'minute'=>[
            'unit'=>'min',
            'group'=>'duration',
            'basic'=>'second',
            'calculate_to'=>function($input) { return $input * 60; },
            'calculate_from'=>function($input) { return $input / 60; }
        ],
        'hour'=>[
            'unit'=>'h',
            'group'=>'duration',
            'basic'=>'second',
            'calculate_to'=>function($input) { return $input * 3600; },
            'calculate_from'=>function($input) { return $input / 3600; }
        ],

// ******************************** Angle ****************************************
        'degree'=>[
            'unit'=>'°',
            'group'=>'angle',
            'basic'=>'degree',
            'calculate_to'=>null,
            'calculate_from'=>null
        ],

// ******************************** Ratio **********************************************
        'percent'=>[
            'unit'=>'%',
            'group'=>'ratio',
            'basic'=>'percent',
            'calculate_to'=>null,
            'calculate_from'=>null
        ],

// ********************************* Capacity ****************************************
        'byte'=>[
            'unit'=>'B',
            'group'=>'capacity',
            'basic'=>'byte',
            'calculate_to'=>null,
            'calculate_from'=>null
        ],
        'kilobyte'=>[
            'unit'=>'KB',
            'group'=>'capacity',
            'basic'=>'byte',
            'calculate_to'=>function($input) { return $input * 1000; },
            'calculate_from'=>function($input) { return $input / 1000; }
        ],
        'megabyte'=>[
            'unit'=>'MB',
            'group'=>'capacity',
            'basic'=>'byte',
            'calculate_to'=>function($input) { return $input * 1000000; },
            'calculate_from'=>function($input) { return $input / 1000000; }
        ],
        
        ];

?>