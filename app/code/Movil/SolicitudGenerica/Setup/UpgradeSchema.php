<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Movil\SolicitudGenerica\Setup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface {

	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
    

        if (version_compare($context->getVersion(), "1.0.0", "<")) {
            $installer = $setup;
            $installer->startSetup();
    
            $table = $setup->getConnection()
            ->newTable($setup->getTable('movistar_solicitud_generic'))
    
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'id'
                    )
                    ->addColumn(
                    'rut',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true, 'default' => ''],
                    'rut'
                    )
                    ->addColumn(
                    'telefono',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true, 'default' => ''],
                    'telefono'
                    )
                    ->addColumn(
                    'email',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => ''],
                    'email'
                    )
                    ->addColumn(
                    'sku_equipo',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable => false'],
                    'sku del equipo'
                    )
                    ->addColumn(
                    'nombre_equipo',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => true, 'default' => '0'],
                    'Nombre del equipo'
                    )
                    ->addColumn(
                    'memoria_interna_equipo',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    20,
                    [],
                    'memoria interna del equipo'
                    )
                    ->addColumn(
                    'id_believe',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true, 'default' => ''],
                    'Id believe'
                    )
                    ->addColumn(
                    'id_scl',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true, 'default' => ''],
                    'id scl del producto'
                    )
                    ->addColumn(
                    'nombre_plan',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => true, 'default' => ''],
                    'Nombre del plan'
                    )
                    ->addColumn(
                    'precio_plan_normal',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    100,
                    ['nullable' => true],
                    'precio normal del plan'
                    )
                    ->addColumn(
                    'precio_plan_oferta',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    100,
                    ['nullable' => true],
                    'precio oferta del plan'
                    )
                    ->addColumn(
                    'tipo_pago',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => true, 'default' => ''],
                    'Tipo de pago'
                    )
                    ->addColumn(
                    'modalidad',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => true, 'default' => ''],
                    'Modalidad'
                    )
                    ->addColumn(
                    'movistar_one',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => true, 'default' => ''],
                    'movistar one equipo'
                    )
                    ->addColumn(
                    'fecha_solicitud',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    100,
                    ['nullable' => true, 'default' => ''],
                    'Fecha de la solicitud'
                    )
                    ->addColumn(
                    'pie_equipo',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => true, 'default' => ''],
                    'pie del equipo'
                    )
                    ->addColumn(
                    'color_equipo',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'color del equipo'
                    )
                    ->addColumn(
                    'numero_cuota',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => true, 'default' => ''],
                    'numero cuota'
                    )
                    ->addColumn(
                    'campana_avatar',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => true, 'default' => ''],
                    'Codigo campana avatar'
                    )
                    ->addColumn(
                    'codigo',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => true, 'default' => ''],
                    'codigo camaleon'
                    )
                    ->addColumn(
                    'tipo_oferta',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    1,
                    ['nullable' => true, 'default' => ''],
                    'Tipo oferta'
                    )
                    ->addColumn(
                    'precio_cuota',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Precio cuota'
                    )
                    ->addColumn(
                    'porcentaje_descuento',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Porcentaje descuento'
                    )
                    ->addColumn(
                    'precio_total',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Precio Total'
                    )
                    ->addColumn(
                    'precio_referencia',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Precio referencia'
                    )
                    ->addColumn(
                    'id_lead',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true, 'default' => ''],
                    'Id lead'
                    )
                    ->addColumn(
                    'metodo_pago',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Metodo de pago'
                    )->addColumn(
                    'precio_pie',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Precio pie'
                    )->addColumn(
                    'numero_cuota',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Numero cuota'
                    )->setComment("tabla formularios genericos movistar");
    
                    //$installer->getConnection()->createTable($table);
                    $setup->getConnection()->createTable($table);
    
                /*    $installer->getConnection()->addIndex(
                    $installer->getTable('movistar_solicitud_generica'),
                    $setup->getIdxName(
                        $installer->getTable('movistar_solicitud_generica'),
                        ['id'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                    ),
                    ['id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                );*/
    
    
              //tabla matriz   
             $table = $setup->getConnection()
            ->newTable($setup->getTable('movistar_matriz_generic'))
    
            /*
             *** Campos generales ***
            */
    
             ->addColumn(
                        'id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'nullable' => false,
                            'primary'  => true,
                            'unsigned' => true,
                        ],
                        'ID'
            )
            ->addColumn(
                    'codigo',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => false, 'default' => ''],
                    'codigo'
                    )
            ->addColumn(
                'oferta_titulo',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Titulo'
            )->addColumn(
                'costo_envio',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Costo de envío'
            )->addColumn(
                'asb_dnis',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Dnis'
            )->addColumn(
                'asb_token',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Token'
            )
            /*
             *** Equipo **
            */
    
            ->addColumn(
                'equipo_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Sku del equipo'
            )->addColumn(
                'equipo_entity_padre',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Entity id'
            )
    
            /*
             *** Oferta ***
            */
    
            ->addColumn(
                'oferta_tipo_solicitud',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Tipo solicitud (modalidad)'
            )->addColumn(
                'oferta_metodo_pago',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Metodo de pago'
            )->addColumn(
                'oferta_precio_total',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Precio total'
            )->addColumn(
                'oferta_precio_pie',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Precio pie'
            )->addColumn(
                'oferta_numero_cuotas',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Numero de cuotas'
            )->addColumn(
                'oferta_precio_cuota',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                5050,
                ['nullable' => false],
                'Precio cuota'
            )->addColumn(
                'oferta_porcentaje_descuento',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Porcentaje de descuento'
            )->addColumn(
                'oferta_precio_referencia',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Precio referencia'
            )->addColumn(
                'oferta_movistar_one',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Movistar one'
            )->addColumn(
                'tipo_oferta',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Tipo oferta'
            )
    
            /*
             *** Planes ***
            */
    
            ->addColumn(
                'plan_codigo',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Codigo plan'
            )->addColumn(
                'plan_duracion_oferta',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Duracion oferta del plan'
            )->addColumn(
                'plan_precio_oferta',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Descuento plan'
            )  ->setComment("tabla formularios genericos movistar");
    
                    //$installer->getConnection()->createTable($table);
                    $setup->getConnection()->createTable($table);
                    $setup->endSetup();
                    
        }

        if (version_compare($context->getVersion(), "1.0.4", "<")) {
            $this->_updateTableSolicitud($setup);
        }
            
    } 

    public function _updateTableSolicitud(SchemaSetupInterface &$setup)
    {
        $connection = $setup->getConnection();
        $tableName = $connection->getTableName("movistar_solicitud_generic");

        $connection->addColumn
        (
            $tableName,
            'utm',
            [
                "type" => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                'nullable' => true,
                "comment" => 'UTM'
            ]
        );

        $connection->addColumn
        (
            $tableName,
            'campaign',
            [
                "type" => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                'nullable' => true,
                "comment" => 'Nombre de campaña'
            ]
        );

        $connection->addColumn
        (
            $tableName,
            'utm_campaign',
            [
                "type" => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                'nullable' => true,
                "comment" => 'utm campaing'
            ]
        );

        $connection->addColumn
        (
            $tableName,
            'utm_source',
            [
                "type" => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                'nullable' => true,
                "comment" => 'utm source'
            ]
        );

        $connection->addColumn
        (
            $tableName,
            'utm_content',
            [
                "type" => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                'nullable' => true,
                "comment" => 'utm content'
            ]
        );

        $connection->addColumn
        (
            $tableName,
            'adgroupid',
            [
                "type" => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                'nullable' => true,
                "comment" => 'adgroupid'
            ]
        );

        $connection->addColumn
        (
            $tableName,
            'keyword',
            [
                "type" => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                'nullable' => true,
                "comment" => 'keyword'
            ]
        );



    } 
}

