DIST=.dist
PLUGIN_NAME=$(shell basename `pwd`)
VERSION=$(shell grep "Version:" ./main.inc.php| sed -e 's/.*: //')
TARGET=../target

.DEFAULT_GOAL := help
.PHONY: help

config: clean manifest  ## prepare environment for building archive
	mkdir -p $(DIST)/$(PLUGIN_NAME)
	cp -pr admin.php BUGS CHANGELOG.md README.md COPYING css imgs include src js language \
	main.inc.php maintain.inc.php MANIFEST public.php template $(DIST)/$(PLUGIN_NAME)/
	find $(DIST) -name '*~' -exec rm \{\} \;

dist: config ## create compressed archive
	cd $(DIST); \
	mkdir -p $(TARGET); \
	rm -f $(TARGET)/$(PLUGIN_NAME)-$(VERSION).zip; \
	zip -v -r9 $(TARGET)/$(PLUGIN_NAME)-$(VERSION).zip $(PLUGIN_NAME); \
	cd ..

manifest:
	@find ./ -type f|egrep -v '(*~|.git|.gitignore|.dist|target|.vscode|composer.*|vendor|bin|rector.php|phpstan*|modele|Makefile|rsync_exclude)'|sed -e 's/\.\///' -e 's/\(.*\)/$(PLUGIN_NAME)\/&/'> ./MANIFEST

clean: ## clean dist directory
	rm -fr $(DIST)

help:
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "; printf "\n  \033[33mUsage:\033[0m\n    make \033[32m[target]\033[0m\n\n"}; {printf "  \033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## /[33m/'
