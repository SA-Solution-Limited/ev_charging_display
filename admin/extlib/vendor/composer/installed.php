<?php return array(
    'root' => array(
        'name' => '__root__',
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'reference' => NULL,
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        '__root__' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'reference' => NULL,
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'components/font-awesome' => array(
            'pretty_version' => '6.1.2',
            'version' => '6.1.2.0',
            'reference' => '2c8dd579722272a218d2cfb510d7ca2d239e48ad',
            'type' => 'component',
            'install_path' => __DIR__ . '/../components/font-awesome',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'components/jquery' => array(
            'pretty_version' => '3.6.0',
            'version' => '3.6.0.0',
            'reference' => '6cf38ee1fd04b6adf8e7dda161283aa35be818c3',
            'type' => 'component',
            'install_path' => __DIR__ . '/../components/jquery',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'twbs/bootstrap' => array(
            'pretty_version' => '5.2.0',
            'version' => '5.2.0.0',
            'reference' => 'edf9c40956d19e6ab3f9151bfe0dfac6be06fa21',
            'type' => 'library',
            'install_path' => __DIR__ . '/../twbs/bootstrap',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'twitter/bootstrap' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '5.2.0',
            ),
        ),
    ),
);
