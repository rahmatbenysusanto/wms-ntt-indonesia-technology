<div class="container-fluid bg-white border-top fixed-bottom">
    <div class="row text-center py-2">
        <div class="col-3">
            <a href="{{ route('inbound.index.mobile') }}" class="text-decoration-none text-dark d-block">
                <i class="mdi mdi-download fs-4 d-block"></i>
                <span class="small">Inbound</span>
            </a>
        </div>
        <div class="col-3">
            <a href="{{ route('inventory.index.mobile') }}" class="text-decoration-none text-dark d-block">
                <i class="mdi mdi-archive fs-4 d-block"></i>
                <span class="small">Inventory</span>
            </a>
        </div>
        <div class="col-3">
            <a href="{{ route('inventory.aging.mobile') }}" class="text-decoration-none text-dark d-block">
                <i class="mdi mdi-timer-sand fs-4 d-block"></i>
                <span class="small">Aging</span>
            </a>
        </div>
        <div class="col-3">
            <a href="{{ route('outbound.index.mobile') }}" class="text-decoration-none text-dark d-block">
                <i class="mdi mdi-upload fs-4 d-block"></i>
                <span class="small">Outbound</span>
            </a>
        </div>
    </div>
</div>
