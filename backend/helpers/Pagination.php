<?php
/**
 * Pagination.php
 * Clase para manejar paginación de resultados
 */

class Pagination {
    private $totalItems;
    private $itemsPerPage;
    private $currentPage;
    private $totalPages;
    private $offset;
    
    /**
     * Constructor
     * 
     * @param int $totalItems Total de ítems
     * @param int $itemsPerPage Ítems por página
     * @param int $currentPage Página actual (por defecto 1)
     */
    public function __construct($totalItems, $itemsPerPage = 12, $currentPage = 1) {
        $this->totalItems = max(0, (int)$totalItems);
        $this->itemsPerPage = max(1, (int)$itemsPerPage);
        $this->currentPage = max(1, (int)$currentPage);
        
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);
        
        // Validar página actual
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        }
        
        // Calcular offset para SQL
        $this->offset = ($this->currentPage - 1) * $this->itemsPerPage;
    }
    
    /**
     * Obtiene el offset para usar en LIMIT de SQL
     */
    public function getOffset() {
        return $this->offset;
    }
    
    /**
     * Obtiene el límite de registros
     */
    public function getLimit() {
        return $this->itemsPerPage;
    }
    
    /**
     * Obtiene SQL LIMIT clause
     */
    public function getLimitSQL() {
        return "LIMIT {$this->itemsPerPage} OFFSET {$this->offset}";
    }
    
    /**
     * Obtiene página actual
     */
    public function getCurrentPage() {
        return $this->currentPage;
    }
    
    /**
     * Obtiene total de páginas
     */
    public function getTotalPages() {
        return $this->totalPages;
    }
    
    /**
     * Obtiene total de ítems
     */
    public function getTotalItems() {
        return $this->totalItems;
    }
    
    /**
     * Verifica si hay página anterior
     */
    public function hasPreviousPage() {
        return $this->currentPage > 1;
    }
    
    /**
     * Verifica si hay página siguiente
     */
    public function hasNextPage() {
        return $this->currentPage < $this->totalPages;
    }
    
    /**
     * Obtiene número de página anterior
     */
    public function getPreviousPage() {
        return max(1, $this->currentPage - 1);
    }
    
    /**
     * Obtiene número de página siguiente
     */
    public function getNextPage() {
        return min($this->totalPages, $this->currentPage + 1);
    }
    
    /**
     * Obtiene rango de ítems en página actual
     * Ej: "1-12 de 48"
     */
    public function getRange() {
        $inicio = $this->offset + 1;
        $fin = min($this->offset + $this->itemsPerPage, $this->totalItems);
        return "{$inicio}-{$fin} de {$this->totalItems}";
    }
    
    /**
     * Genera URL con parámetro de página
     */
    public static function generatePageUrl($baseUrl, $page, $params = []) {
        $params['pagina'] = $page;
        $query = http_build_query($params);
        return $baseUrl . ($query ? '?' . $query : '');
    }
    
    /**
     * Obtiene números de página para paginador
     * (Ej: [1, 2, 3, ..., 10])
     */
    public function getPageNumbers($maxButtons = 7) {
        $pages = [];
        
        if ($this->totalPages <= $maxButtons) {
            // Mostrar todas las páginas
            for ($i = 1; $i <= $this->totalPages; $i++) {
                $pages[] = $i;
            }
        } else {
            // Mostrar con puntos suspensivos
            $mitad = floor($maxButtons / 2);
            
            // Siempre mostrar primera página
            $pages[] = 1;
            
            // Calcular rango alrededor de página actual
            $inicio = max(2, $this->currentPage - $mitad);
            $fin = min($this->totalPages - 1, $this->currentPage + $mitad);
            
            // Agregar puntos suspensivos
            if ($inicio > 2) {
                $pages[] = '...';
            }
            
            // Agregar rango
            for ($i = $inicio; $i <= $fin; $i++) {
                $pages[] = $i;
            }
            
            // Agregar puntos suspensivos
            if ($fin < $this->totalPages - 1) {
                $pages[] = '...';
            }
            
            // Siempre mostrar última página
            $pages[] = $this->totalPages;
        }
        
        return $pages;
    }
    
    /**
     * Genera HTML del paginador
     */
    public function renderHTML($baseUrl, $params = []) {
        if ($this->totalPages <= 1) {
            return ''; // No mostrar paginador si hay una sola página
        }
        
        $html = '<nav class="pagination-nav" role="navigation" aria-label="Paginación">';
        $html .= '<ul class="pagination">';
        
        // Botón anterior
        if ($this->hasPreviousPage()) {
            $prevUrl = self::generatePageUrl($baseUrl, $this->getPreviousPage(), $params);
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($prevUrl) . '">← Anterior</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">← Anterior</span></li>';
        }
        
        // Números de página
        foreach ($this->getPageNumbers() as $page) {
            if ($page === '...') {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            } else {
                $isActive = ($page == $this->currentPage) ? 'active' : '';
                $pageUrl = self::generatePageUrl($baseUrl, $page, $params);
                $html .= '<li class="page-item ' . $isActive . '"><a class="page-link" href="' . htmlspecialchars($pageUrl) . '">' . $page . '</a></li>';
            }
        }
        
        // Botón siguiente
        if ($this->hasNextPage()) {
            $nextUrl = self::generatePageUrl($baseUrl, $this->getNextPage(), $params);
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($nextUrl) . '">Siguiente →</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Siguiente →</span></li>';
        }
        
        $html .= '</ul>';
        $html .= '</nav>';
        
        return $html;
    }
}
