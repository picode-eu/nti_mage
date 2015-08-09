<?php
class Picode_LinksToTopmenu_Model_Observer
{
    public function addToTopmenu(Varien_Event_Observer $observer)
    {
        $parseCurentUrl = parse_url(Mage::helper('core/url')->getCurrentUrl());
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        
        /*******************************************************************/
        
        // level zero
        $nodePath = 'conturifurnizori/furnizori/';
        $node = new Varien_Data_Tree_Node(array(
                'name'   => 'Furnizori',
                'id'     => 'furnizori',
                'url'    => Mage::getUrl($nodePath),
        ), 'id', $tree, $menu);
        
        
        if (strpos($parseCurentUrl['path'], $nodePath) !== false) {
            $node->setData('class', 'active');
        }
     
        $menu->addChild($node);
        
        // level one child of furnizori
        $data = array(
            'name'   => 'Servicii Foto',
            'id'     => 'foto',
            'url'    => Mage::getUrl('conturifurnizori/furnizori/foto/'),
        );
 
        $subNode = new Varien_Data_Tree_Node($data, 'id', $tree, $node);
        $node->addChild($subNode);
        
        // level one child of portofoliu
        $data = array(
            'name'   => 'Servicii Video',
            'id'     => 'video',
            'url'    => Mage::getUrl('conturifurnizori/furnizori/video/'),
        );
 
        $subNode = new Varien_Data_Tree_Node($data, 'id', $tree, $node);
        $node->addChild($subNode);
        
        // level one child of portofoliu
        $data = array(
            'name'   => 'Servicii Foto-Video',
            'id'     => 'foto-video',
            'url'    => Mage::getUrl('conturifurnizori/furnizori/fotovideo/'),
        );
 
        $subNode = new Varien_Data_Tree_Node($data, 'id', $tree, $node);
        $node->addChild($subNode);
        
        /*******************************************************************/
        
        // level zero
        $nodePath = 'portofoliu';
        $node = new Varien_Data_Tree_Node(array(
                'name'   => 'Portofolii',
                'id'     => 'portofolii',
                // 'url'    => Mage::getUrl($nodePath),
                'url'    => 'javascript:void(0)',
        ), 'id', $tree, $menu);
        
        
        if (strpos($parseCurentUrl['path'], $nodePath) !== false) {
            $node->setData('class', 'active');
        }
     
        $menu->addChild($node);

        // level one child of portofoliu
        /*
        $data = array(
            'name'   => 'Disponibile in curand',
            'id'     => 'albume-foto',
            'url'    =>'javascript:void(0)',
        );

        $subNode = new Varien_Data_Tree_Node($data, 'id', $tree, $node);
        $node->addChild($subNode);
        */

        // level one child of portofoliu
        $data = array(
            'name'   => 'Albume Foto',
            'id'     => 'albume-foto',
            'url'    => Mage::getUrl('portofoliu/foto'),
        );

        $subNode = new Varien_Data_Tree_Node($data, 'id', $tree, $node);
        $node->addChild($subNode);
        
        // level one child of portofoliu
        $data = array(
            'name'   => 'Videoclipuri',
            'id'     => 'videoclipuri',
            'url'    => Mage::getUrl('portofoliu/video'),
        );

        $subNode = new Varien_Data_Tree_Node($data, 'id', $tree, $node);
        $node->addChild($subNode);
        
        /*******************************************************************/
        
        // level zero
        $nodePath = 'articole-utile';
        $node = new Varien_Data_Tree_Node(array(
                'name'   => 'Articole Utile',
                'id'     => 'articole-utile',
                'url'    => Mage::getUrl($nodePath),
        ), 'id', $tree, $menu);
        
        if (strpos($parseCurentUrl['path'], $nodePath) !== false) {
            $node->setData('class', 'active');
        }
        
        $menu->addChild($node);
        
        // level zero
        $nodePath = 'search';
        $node = new Varien_Data_Tree_Node(array(
                'name'   => 'Search',
                'id'     => 'search',
                'url'    => 'javascript:void(0)',
                'class'  => 'fa fa-search top-search',
        ), 'id', $tree, $menu);
        
        $menu->addChild($node);
        
        /*******************************************************************/
        
        return $this;
    }
}
