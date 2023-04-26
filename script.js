const levelNumberInput = document.getElementById('level-number');
const levelNameInput = document.getElementById('level-name');
const levelDescriptionInput = document.getElementById('level-description');
const grid = document.getElementById('grid');
const generateXmlBtn = document.getElementById('generate-xml');
const xmlOutput = document.getElementById('xml-output');
const toggleDrawBtn = document.getElementById('toggle-draw');
const enemyButtons = document.getElementById('enemy-buttons');
const enemyTypes = ['Horse', 'Ghost', 'Teleporter', 'Bouncer', 'Chaser'];
const collectableButtons = document.getElementById('collectable-buttons');
const collectables = ['Key', 'Life', 'Coin', 'Finish'];
const gridHeight = 50;
const gridWidth = 120;

const State = {
    SELECT_ACTION: 'select_action',
    PLACE_WALL: 'place_wall',
    PLACE_ENEMY: 'place_enemy',
    PLACE_COLLECTABLE: 'place_collectable',
    JOIN_KEY_WALL: 'join_key_wall',
    SELECT_COLLECTABLE_WIN_CONDITION: 'select_collectable_win_condition',
    REMOVE: 'remove'
};

const Tooltip = {
    SELECT_ACTION: null,
    PLACE_WALL: 'Click the grid to place walls',
    PLACE_ENEMY: 'Click the grid to place enemies',
    PLACE_COLLECTABLE: 'Click the grid to place collectables',
    JOIN_KEY_WALL: 'Click a wall to join it to the key',
    SELECT_COLLECTABLE_WIN_CONDITION: 'Click a collectable to set it as the win condition',
    REMOVE: 'Click the grid to remove walls, enemies, and collectables'    
};

let lastPlacedKeyCell;

let selectedCollectable = null;
let selectedEnemy = null;
let isMouseDown = false;
let canDraw = false;
let currentState = State.SELECT_ACTION;


const collectableSymbols = {
    Key: '🔑',
    Life: '❤️',
    Coin: '$',
    Finish: 'F'
};

const enemySymbols = {
    Horse: '🐎',
    Ghost: '👻',
    Teleporter: '📡',
    Bouncer: '🏓',
    Chaser: '🏃‍'
};

function createBorderWalls() {
    for (let i = 0; i < gridHeight; i++) {
        for (let j = 0; j < gridWidth; j++) {
            if (i === 0 || i === gridHeight - 1 || j === 0 || j === gridWidth - 1) {
                const cell = document.querySelector(`.cell[data-top="${i}"][data-left="${j}"]`);
                cell.classList.add('wall');
            }
        }
    }
}

function createGrid() {
    for (let i = 0; i < gridHeight; i++) {
        for (let j = 0; j < gridWidth; j++) {
            const cell = document.createElement('div');
            cell.classList.add('cell');
            cell.dataset.top = i;
            cell.dataset.left = j;
            grid.appendChild(cell);
        }
    }
}

function toggleDraw() {
    canDraw = !canDraw;
    toggleDrawBtn.textContent = canDraw ? 'Disable Drawing' : 'Enable Drawing';

    // Update currentState when toggleDraw is called
    currentState = canDraw ? State.PLACE_WALL : State.SELECT_ACTION;
}

function addEnemyButtons() {
    enemyTypes.forEach((enemyType) => {
        const enemyButton = document.createElement('button');
        enemyButton.textContent = `Add ${enemyType}`;
        enemyButton.addEventListener('click', () => {
            // Reset canDraw and selectedCollectable
            canDraw = false;
            toggleDrawBtn.textContent = 'Enable Drawing';
            selectedCollectable = null;

            // Set selectedEnemy
            selectedEnemy = selectedEnemy === enemyType ? null : enemyType;

            // Update currentState when an enemy button is clicked
            currentState = selectedEnemy ? State.PLACE_ENEMY : State.SELECT_ACTION;
        });
        enemyButtons.appendChild(enemyButton);
    });
}

function addCollectableButtons() {
    collectables.forEach((collectableType) => {
        const collectableButton = document.createElement('button');
        collectableButton.textContent = `Add ${collectableType}`;
        collectableButton.addEventListener('click', () => {
            // Reset canDraw and selectedEnemy
            canDraw = false;
            toggleDrawBtn.textContent = 'Enable Drawing';
            selectedEnemy = null;

            // Set selectedCollectable
            selectedCollectable = selectedCollectable === collectableType ? null : collectableType;

            // Update currentState when a collectable button is clicked
            currentState = selectedCollectable ? State.PLACE_COLLECTABLE : State.SELECT_ACTION;

            if (currentState === State.SELECT_ACTION) {
                lastPlacedKeyCell = null;
            }
        });
        collectableButtons.appendChild(collectableButton);
    });
}

function handleCellClick(e) {
    const cell = e.target;
    if (cell.classList.contains('cell')) {
        if (currentState === State.PLACE_WALL) {
            cell.classList.toggle('wall');
        } else if (currentState === State.PLACE_ENEMY && selectedEnemy) {
            cell.dataset.enemy = selectedEnemy;
            cell.textContent = enemySymbols[selectedEnemy];
        } else if (currentState === State.PLACE_COLLECTABLE && selectedCollectable) {
            if (selectedCollectable === 'Key') {
                lastPlacedKeyCell = cell;
                currentState = State.JOIN_KEY_WALL;
            }
            cell.dataset.collectable = selectedCollectable;
            cell.textContent = collectableSymbols[selectedCollectable];
        } else if (currentState === State.JOIN_KEY_WALL && cell.classList.contains('wall')) {
            cell.style.backgroundColor = 'red';
            lastPlacedKeyCell.dataset.connectedWall = `${cell.dataset.top}-${cell.dataset.left}`;
            currentState = State.SELECT_ACTION;
        }
    }
}

function handleCellMouseOver(e) {
    if (isMouseDown && canDraw) {
        handleCellClick(e);
    }
}

function generateXml() {
    const levelNumber = levelNumberInput.value;
    const levelName = levelNameInput.value;
    const levelDescription = levelDescriptionInput.value;

    let xml = `<?xml version="1.0" encoding="UTF-8"?>\n<levels>\n`;
    xml += `<level number="${levelNumber}" name="${levelName}" description="${levelDescription}">\n`;

    let wallsXml = `  <walls>\n`;
    let enemiesXml = `  <enemies>\n`;
    let collectablesXml = `  <collectables>\n`;

    const cells = document.querySelectorAll('.cell');
    cells.forEach((cell) => {
        const top = cell.dataset.top;
        const left = cell.dataset.left;

        if (cell.classList.contains('wall')) {
            wallsXml += `    <wall top="${top}" left="${left}" />\n`;
        }

        if (cell.dataset.enemy) {
            enemiesXml += `    <enemy type="${cell.dataset.enemy}" top="${top}" left="${left}" />\n`;
        }

        if (cell.dataset.collectable) {
            if (cell.dataset.collectable === 'Key' && cell.dataset.connectedWall) {
                collectablesXml += `    <collectable type="${cell.dataset.collectable}" top="${top}" left="${left}" connectedWall="${cell.dataset.connectedWall}" />\n`;
            } else {
                collectablesXml += `    <collectable type="${cell.dataset.collectable}" top="${top}" left="${left}" />\n`;
            }
        }
    });

    wallsXml += `  </walls>\n`;
    enemiesXml += `  </enemies>\n`;
    collectablesXml += `  </collectables>\n`;

    xml += wallsXml;
    xml += enemiesXml;
    xml += collectablesXml;
    xml += `</level>\n</levels>\n`;
    xmlOutput.value = xml;
}


grid.addEventListener('mousedown', (e) => {
    isMouseDown = true;
    handleCellClick(e);
});

grid.addEventListener('mouseup', () => {
    isMouseDown = false;
});

grid.addEventListener('mouseover', handleCellMouseOver);
toggleDrawBtn.addEventListener('click', toggleDraw);
generateXmlBtn.addEventListener('click', generateXml);

createGrid();
createBorderWalls();
addEnemyButtons();
addCollectableButtons();
