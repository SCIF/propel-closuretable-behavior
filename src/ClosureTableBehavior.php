<?php

namespace SCIF\Propel\Behavior;

use Propel\Generator\Exception\InvalidArgumentException;
use Propel\Generator\Model\Behavior;
use Propel\Generator\Model\Column;
use Propel\Generator\Model\Domain;
use Propel\Generator\Model\ForeignKey;
use Propel\Generator\Model\Index;
use Propel\Generator\Model\Table;

class ClosureTableBehavior extends Behavior {
    // default parameters value
    protected $parameters = array(
        'id_fieldname'  => null,
        'depth_name'    => 'depth',
        'parent_name'   => 'parent_id',
        'closure_table' => null,
        'ct_ancestor_name'   => 'ancestor',
        'ct_descendant_name' => 'descendant',
        'ct_depth_name'      => 'depth',
    );

    public function modifyTable()
    {
        $table = $this->getTable();
        $ct_tablename = $this->getParameter('closure_table') ?: $table->getName().'_closures';
        $database = $table->getDatabase();

        if ($this->getParameter('id_fieldname')) {
            $id_fieldname = $this->getParameter('id_fieldname');
        } elseif (!$table->hasPrimaryKey()) {
            throw new InvalidArgumentException('ClosureTableBehavior: PK is not found for table «'.$table->getName().'». Closure table can not be created. Possible solutions: please set parameter «id_fieldname» explicitly or define PK on table');
        } elseif ($table->hasCompositePrimaryKey()) {
            throw new InvalidArgumentException('ClosureTableBehavior: Composite PK is not allowed to use as id column. Please set parameter «id_fieldname» explicitly');
        } else {
            $id_fieldname = $table->getFirstPrimaryKeyColumn()->getName();
        }

        if (!$database->hasTable($ct_tablename)) {
            $ct_table = new Table($ct_tablename);

            $database->addTable($ct_table);
        } else {
            $ct_table = $database->getTable($ct_tablename);
        }

        $this->addClosureColumn($this->getParameter('ct_descendant_name'), $ct_table, $table->getColumn($id_fieldname));
        $this->addClosureColumn($this->getParameter('ct_ancestor_name'), $ct_table, $table->getColumn($id_fieldname));
        $this->addColumn($ct_table, $this->getParameter('ct_depth_name'));
        $this->addColumn($table, $this->getParameter('depth_name'));
        $this->addParentColumn($table, $id_fieldname);
return;
        echo 23423;
        exit;
        if (!$columnName = $this->getParameter('name')) {
            throw new InvalidArgumentException(sprintf(
                'You must define a \'name\' parameter for the \'aggregate_column\' behavior in the \'%s\' table',
                $table->getName()
            ));
        }
        // add the aggregate column if not present
        if(!$table->hasColumn($columnName)) {
            $table->addColumn(array(
                'name'    => $columnName,
                'type'    => 'integer',
            ));
        }
    }

    protected function addClosureColumn($name, Table $ct_table, Column $column)
    {
        $table = $this->getTable();
        $id_fieldname = $column->getName();
        $domain = $column->getDomain();

        if (!$ct_table->hasColumn($name)) {
            $column = new Column($name);
            $column->setDomain($domain);
            $column->setPrimaryKey(true);
            $ct_table->addColumn($column);
        } else {
            $column = $ct_table->getColumn($name);
        }

        $ct_tablename_normalized = str_replace('_', '', $ct_table->getName());
        $fk_name = $ct_tablename_normalized.'_'.$name.'_fk';

        if (!$ct_table->getColumnForeignKeys($name)) {
            $column_fk = new ForeignKey($fk_name);
            $column_fk->addReference($name, $table->getColumn($id_fieldname)->getName());
            $column_fk->setForeignTableCommonName($table->getName());
            $column_fk->setOnUpdate('cascade');
            $column_fk->setOnDelete('restrict');
            $ct_table->addForeignKey($column_fk);
        }

        $column_idx_name = $fk_name.'_idx';

        if (!$ct_table->hasIndex($column_idx_name)) {
            $column_idx = new Index($column_idx_name);
            $column_idx->addColumn(['name' => $column->getName()]);
            $ct_table->addIndex($column_idx);
        }
    }

    protected function addColumn(Table $table, $name)
    {
        if (!$table->hasColumn($name)) {
            $column = new Column($name);
            // don't know how to define unsigned :(
            $domain = new Domain('TINYINT', 'tinyint(3) unsigned');
            $column->setDomain($domain);

            $table->addColumn($column);

            $column_idx_name = $name.'_idx';

            if (!$table->hasIndex($column_idx_name)) {
                $column_idx = new Index($column_idx_name);
                $column_idx->addColumn(['name' => $column->getName()]);
                $table->addIndex($column_idx);
            }

        }
    }

    protected function addParentColumn(Table $table, $id_name)
    {
        $name = $this->getParameter('parent_name');
//Остановился тут
        if (!$table->getColumnForeignKeys($name)) {
            //var_dump($table->getColumnForeignKeys('parent_id'));
            //foreach($table->getForeignKeys() as $column) {
            //    var_dump($column->getForeignColumns(), $column->getName());
            //}
        }
        foreach($table->getDatabase()->getTables() as $table) {
            //echo $table->getName()."\n";
        }
        //exit;
    }

}