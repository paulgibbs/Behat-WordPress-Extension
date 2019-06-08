#!/bin/bash
#set -euo pipefail
#IFS=$'\n\t'

# Paths relative to root.

IFS=$'\n' BEHAT_STEPS=($(vendor/bin/behat --definitions l --no-colors))
XLIFF_OUTPUT=template.xliff

# Replace all non-alnum characters with a hypen.
function sanitise() {
  local name="$1"
  name="${name//[^[:alnum:]]/-}"

  # Convert to lowercase, strip repeated hypens.
  echo "$name" | tr -s '[:upper:]' '[:lower:]' | tr -s '-'
}

# Header.
(cat <<TEMPLATE
<?xml version="1.0"?>
<xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
  <file source-language="en" datatype="plaintext" original="global" target-language="replace-me">
    <body>
TEMPLATE
) > $XLIFF_OUTPUT

# Translation strings.
for STEP in "${BEHAT_STEPS[@]}"; do
  # Remove "default | | from step definition.
  STEP=${STEP##*| }
  STEP_ID=$(sanitise $STEP)

  (cat <<TEMPLATE
      <trans-unit id="$STEP_ID">
        <source><![CDATA[$STEP]]></source>
        <target><![CDATA[]]></target>
      </trans-unit>
TEMPLATE
) >> $XLIFF_OUTPUT
done

# Footer.
(cat <<TEMPLATE
    </body>
  </file>
</xliff>
TEMPLATE
) >> $XLIFF_OUTPUT

echo "Done!"
